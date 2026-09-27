<?php

namespace App\View\Composers;

use App\Models\ServiceCategory;
use App\Services\Seo\SeoManager;
use App\Support\SiteSettings;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Throwable;

/**
 * Shares navigation and settings data with the public layout and error pages.
 */
class SiteComposer
{
    /**
     * @var Collection<int, ServiceCategory>|null
     */
    protected ?Collection $categories = null;

    public function __construct(protected SiteSettings $settings, protected SeoManager $seo) {}

    public function compose(View $view): void
    {
        $locale = app()->getLocale();

        $view->with([
            'locale' => $locale,
            'dir' => config("site.locales.$locale.dir", 'rtl'),
            'site' => $this->settings,
            'seo' => $this->seo,
            'navCategories' => $this->categories(),
        ]);
    }

    /**
     * @return Collection<int, ServiceCategory>
     */
    protected function categories(): Collection
    {
        try {
            return $this->categories ??= ServiceCategory::query()
                ->with(['publishedServices:id,service_category_id,slug,title_ar,title_en,icon,sort'])
                ->orderBy('sort')
                ->get()
                ->filter(fn (ServiceCategory $category): bool => $category->publishedServices->isNotEmpty())
                ->values();
        } catch (Throwable) {
            return collect();
        }
    }
}
