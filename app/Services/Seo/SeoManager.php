<?php

namespace App\Services\Seo;

use App\Models\Media;
use App\Models\SeoMeta;
use App\Support\TextFormatter;

/**
 * Collects per-request metadata (title, description, social image) for the page head.
 */
class SeoManager
{
    protected ?string $title = null;

    protected ?string $description = null;

    protected ?Media $image = null;

    protected bool $isHome = false;

    /**
     * Load the admin-managed metadata of a static page.
     */
    public function forPage(string $page): static
    {
        $meta = SeoMeta::with('ogMedia')->where('page', $page)->first();

        $this->title = $meta?->tr('title');
        $this->description = $meta?->tr('description');
        $this->image = $meta?->ogMedia;
        $this->isHome = $page === 'home';

        return $this;
    }

    public function title(?string $title): static
    {
        $this->title = filled($title) ? $title : $this->title;

        return $this;
    }

    public function description(?string $description): static
    {
        if (filled($description)) {
            $this->description = TextFormatter::excerpt($description, 160);
        }

        return $this;
    }

    public function image(?Media $image): static
    {
        $this->image = $image ?? $this->image;

        return $this;
    }

    /**
     * Full document title, e.g. "Services | Noor AlQaseem".
     */
    public function documentTitle(): string
    {
        $brand = site('short_name') ?? config('app.name');

        if ($this->title === null) {
            return $brand;
        }

        return $this->isHome || str_contains($this->title, $brand) ? $this->title : $this->title.' | '.$brand;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Absolute URL of the social sharing image (Open Graph requires absolute URLs).
     */
    public function imageUrl(): string
    {
        return $this->image ? url($this->image->ogUrl()) : asset('images/og-default.jpg');
    }
}
