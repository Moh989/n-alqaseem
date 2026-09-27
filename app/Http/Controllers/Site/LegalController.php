<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use App\Services\Seo\SeoManager;
use Illuminate\View\View;

class LegalController extends Controller
{
    public function privacy(SeoManager $seo): View
    {
        return $this->render('privacy', $seo);
    }

    public function terms(SeoManager $seo): View
    {
        return $this->render('terms', $seo);
    }

    protected function render(string $page, SeoManager $seo): View
    {
        $seo->forPage($page);

        $sections = PageSection::where('page', $page)->where('is_published', true)->orderBy('sort')->orderBy('id')->get();

        return view('site.legal', [
            'page' => $page,
            'sections' => $sections,
            'updatedAt' => $sections->max('updated_at'),
        ]);
    }
}
