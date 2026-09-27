<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Support\LocaleUrl;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * XML sitemap with hreflang alternates for both languages.
     */
    public function __invoke(): Response
    {
        $pages = collect(['home', 'about', 'services.index', 'contact', 'privacy', 'terms'])
            ->map(fn (string $name): array => ['name' => $name, 'parameters' => []]);

        $services = Service::published()->orderBy('sort')->get(['slug', 'updated_at'])
            ->map(fn (Service $service): array => [
                'name' => 'services.show',
                'parameters' => ['service' => $service->slug],
                'lastmod' => $service->updated_at,
            ]);

        $entries = $pages->concat($services)->flatMap(function (array $page): array {
            $alternates = collect(LocaleUrl::locales())
                ->mapWithKeys(fn (string $locale): array => [$locale => LocaleUrl::route($page['name'], $page['parameters'], $locale)]);

            return $alternates->map(fn (string $url): array => [
                'loc' => $url,
                'alternates' => $alternates,
                'default' => $alternates[config('site.default_locale')],
                'lastmod' => $page['lastmod'] ?? null,
            ])->values()->all();
        });

        return response()
            ->view('site.sitemap', ['entries' => $entries])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
