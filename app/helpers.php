<?php

use App\Support\LocaleUrl;
use App\Support\SiteSettings;

if (! function_exists('lroute')) {
    /**
     * Generate a URL to a named public route in the given (or current) locale.
     *
     * @param  array<string, mixed>  $parameters
     */
    function lroute(string $name, array $parameters = [], ?string $locale = null, bool $absolute = true): string
    {
        return LocaleUrl::route($name, $parameters, $locale, $absolute);
    }
}

if (! function_exists('site')) {
    /**
     * Get the site settings repository, or an approved setting value by key.
     */
    function site(?string $key = null, ?string $locale = null): SiteSettings|string|null
    {
        $settings = app(SiteSettings::class);

        return $key === null ? $settings : $settings->get($key, $locale);
    }
}
