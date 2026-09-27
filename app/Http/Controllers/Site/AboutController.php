<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use App\Models\Slide;
use App\Services\Seo\SeoManager;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __invoke(SeoManager $seo): View
    {
        $seo->forPage('about');

        return view('site.about', [
            'slides' => Slide::with('media')->where('page', 'about')->where('is_published', true)->orderBy('sort')->get(),
            'sections' => PageSection::with('items')->where('page', 'about')->where('is_published', true)->orderBy('sort')->get()->keyBy('key'),
            'hasProfilePdf' => site()->hasProfilePdf(),
        ]);
    }
}
