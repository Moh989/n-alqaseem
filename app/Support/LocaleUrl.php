<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

/**
 * Builds URLs for the Arabic (root) and English (/en) versions of public routes.
 */
class LocaleUrl
{
    /**
     * Routes that have no page of their own and map to another route for language switching.
     *
     * @var array<string, string>
     */
    protected const ALIASES = ['contact.store' => 'contact'];

    /**
     * @return list<string>
     */
    public static function locales(): array
    {
        return array_keys(config('site.locales'));
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function route(string $name, array $parameters = [], ?string $locale = null, bool $absolute = true): string
    {
        return route(self::routeName($name, $locale ?? app()->getLocale()), $parameters, $absolute);
    }

    public static function routeName(string $name, string $locale): string
    {
        return $locale === config('site.default_locale') ? $name : $locale.'.'.$name;
    }

    /**
     * Strip the locale prefix from a route name.
     */
    public static function baseName(string $name): string
    {
        foreach (self::locales() as $locale) {
            if (str_starts_with($name, $locale.'.')) {
                return substr($name, strlen($locale) + 1);
            }
        }

        return $name;
    }

    /**
     * URL of the page currently being viewed, in another locale.
     */
    public static function alternate(string $locale): string
    {
        $route = request()->route();
        $name = $route?->getName();

        if ($name === null || str_starts_with($name, 'admin.')) {
            return self::route('home', [], $locale);
        }

        $base = self::baseName($name);
        $base = self::ALIASES[$base] ?? $base;

        if (! Route::has(self::routeName($base, $locale))) {
            return self::route('home', [], $locale);
        }

        $parameters = collect($route->parameters())
            ->map(function (mixed $value, string $key) use ($route): mixed {
                if (! $value instanceof Model) {
                    return $value;
                }

                $field = $route->bindingFieldFor($key);

                return $field ? $value->getAttribute($field) : $value->getRouteKey();
            })
            ->all();

        return self::route($base, $parameters, $locale);
    }

    /**
     * Canonical URL of the current page (no query string).
     */
    public static function canonical(): string
    {
        return self::alternate(app()->getLocale());
    }
}
