<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Slide;
use App\Services\Seo\SeoManager;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(SeoManager $seo): View
    {
        $seo->forPage('services');

        return view('site.services.index', [
            'slides' => Slide::with('media')->where('page', 'services')->where('is_published', true)->orderBy('sort')->get(),
            'intro' => PageSection::where('page', 'services')->where('key', 'intro')->where('is_published', true)->first(),
            'categories' => ServiceCategory::with('publishedServices.media')->orderBy('sort')->get()
                ->filter(fn (ServiceCategory $category): bool => $category->publishedServices->isNotEmpty()),
        ]);
    }

    public function show(Service $service, SeoManager $seo): View
    {
        abort_unless($service->is_published, 404);

        $service->load(['category', 'media']);

        $seo->forPage('services')
            ->title($service->tr('seo_title') ?? $service->tr('title'))
            ->description($service->tr('seo_description') ?? $service->tr('summary'))
            ->image($service->media);

        return view('site.services.show', [
            'service' => $service,
            'related' => Service::published()
                ->where('service_category_id', $service->service_category_id)
                ->whereKeyNot($service->getKey())
                ->orderBy('sort')
                ->get(),
        ]);
    }
}
