<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['seed_key', 'directory', 'original_name', 'width', 'height', 'bytes', 'widths', 'alt_ar', 'alt_en', 'is_stock', 'credit', 'source_url', 'license', 'created_by'])]
class Media extends Model
{
    protected $table = 'media';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'widths' => 'array',
            'is_stock' => 'boolean',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function url(int $width): string
    {
        return Storage::disk('public')->url($this->directory.'/w'.$width.'.webp');
    }

    public function largestWidth(): int
    {
        return (int) max($this->widths ?: [$this->width]);
    }

    public function srcset(): string
    {
        return collect($this->widths)
            ->sort()
            ->map(fn (int $width): string => $this->url($width).' '.$width.'w')
            ->implode(', ');
    }

    public function fallbackUrl(): string
    {
        return Storage::disk('public')->url($this->directory.'/fallback.jpg');
    }

    public function ogUrl(): string
    {
        return Storage::disk('public')->url($this->directory.'/og.jpg');
    }

    /**
     * Height of the fallback rendition, used for intrinsic sizing of <img>.
     */
    public function heightFor(int $width): int
    {
        return (int) round($this->height * $width / max(1, $this->width));
    }

    public function alt(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return (string) ($this->getAttribute('alt_'.$locale) ?? '');
    }

    /**
     * Determine whether any record still points at this media item.
     */
    public function isReferenced(): bool
    {
        return Slide::where('media_id', $this->id)->exists()
            || Service::where('media_id', $this->id)->exists()
            || SeoMeta::where('og_media_id', $this->id)->exists();
    }

    /**
     * Delete the stored renditions and the record when nothing references it any more.
     */
    public function deleteIfUnused(): void
    {
        if ($this->isReferenced()) {
            return;
        }

        Storage::disk('public')->deleteDirectory($this->directory);
        $this->delete();
    }
}
