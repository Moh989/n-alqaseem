<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Apply the locale given by the route group and share direction data with the views.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $locale): Response
    {
        self::apply($locale);

        return $next($request);
    }

    public static function apply(string $locale): void
    {
        $locales = config('site.locales');
        $locale = array_key_exists($locale, $locales) ? $locale : config('site.default_locale');

        app()->setLocale($locale);
        View::share('locale', $locale);
        View::share('dir', $locales[$locale]['dir']);
    }
}
