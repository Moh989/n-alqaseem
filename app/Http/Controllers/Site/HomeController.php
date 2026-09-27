<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use App\Models\ServiceCategory;
use App\Models\Slide;
use App\Services\Seo\SeoManager;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(SeoManager $seo): View
    {
        $seo->forPage('home');

        return view('site.home', [
            'slides' => Slide::with('media')->where('page', 'home')->where('is_published', true)->orderBy('sort')->get(),
            'sections' => PageSection::where('page', 'home')->where('is_published', true)->get()->keyBy('key'),
            'process' => PageSection::with('items')->where('page', 'about')->where('key', 'process')->where('is_published', true)->first(),
            'categories' => ServiceCategory::with('publishedServices.media')->orderBy('sort')->get()
                ->filter(fn (ServiceCategory $category): bool => $category->publishedServices->isNotEmpty()),
            'hasProfilePdf' => site()->hasProfilePdf(),
        ]);
    }
}
