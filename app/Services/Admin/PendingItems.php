<?php

namespace App\Services\Admin;

use App\Models\PageSection;
use App\Models\SeoMeta;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Slide;
use Illuminate\Support\Collection;

/**
 * Everything that is incomplete or not yet approved, and therefore not shown publicly.
 */
class PendingItems
{
    /**
     * @return Collection<int, array{label: string, url: string}>
     */
    public function all(): Collection
    {
        $items = collect();

        Setting::query()->orderBy('sort')->get()
            ->filter(fn (Setting $setting): bool => ! $setting->isApproved() || ! $setting->isFilled())
            ->filter(fn (Setting $setting): bool => ! str_starts_with($setting->key, 'social_') || filled($setting->value))
            ->reject(fn (Setting $setting): bool => $setting->type === 'boolean')
            ->each(function (Setting $setting) use ($items): void {
                $items->push([
                    'label' => __('admin.pending.setting', ['label' => $setting->label()]),
                    'url' => $setting->group === 'documents' ? route('admin.profile-pdf.index') : route('admin.settings.index').'#setting-'.$setting->key,
                ]);
            });

        Service::query()->orderBy('sort')->get()->each(function (Service $service) use ($items): void {
            if (! $service->media_id) {
                $items->push(['label' => __('admin.pending.service_image', ['title' => $service->title_ar]), 'url' => route('admin.services.edit', $service)]);
            }

            if (! $service->hasBothLanguages('title') || ! $service->hasBothLanguages('body')) {
                $items->push(['label' => __('admin.pending.service_english', ['title' => $service->title_ar]), 'url' => route('admin.services.edit', $service)]);
            }
        });

        Slide::query()->whereNull('media_id')->orderBy('page')->orderBy('sort')->get()->each(function (Slide $slide) use ($items): void {
            $items->push(['label' => __('admin.pending.slide_image', ['title' => $slide->heading_ar]), 'url' => route('admin.slides.edit', $slide)]);
        });

        SeoMeta::query()->get()
            ->filter(fn (SeoMeta $meta): bool => ! $meta->hasBothLanguages('description'))
            ->each(function (SeoMeta $meta) use ($items): void {
                $items->push(['label' => __('admin.pending.seo_description', ['page' => SeoMeta::PAGES[$meta->page] ?? $meta->page]), 'url' => route('admin.seo.edit', $meta->page)]);
            });

        $legalReviewed = Setting::where('key', 'legal_review')->where('status', Setting::STATUS_APPROVED)->exists();

        foreach ($legalReviewed ? [] : ['privacy', 'terms'] as $page) {
            $items->push([
                'label' => __('admin.pending.legal_review', ['page' => PageSection::PAGES[$page]['ar']]),
                'url' => route('admin.pages.edit', $page),
            ]);
        }

        return $items;
    }
}
