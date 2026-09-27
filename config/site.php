<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Locales
    |--------------------------------------------------------------------------
    |
    | Arabic is the primary language and is served at the site root. English is
    | served under the /en prefix with the same (English) slugs.
    |
    */

    'default_locale' => 'ar',

    'locales' => [
        'ar' => ['name' => 'العربية', 'dir' => 'rtl', 'og' => 'ar_IQ'],
        'en' => ['name' => 'English', 'dir' => 'ltr', 'og' => 'en_US'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Slide call-to-action targets
    |--------------------------------------------------------------------------
    |
    | Slide buttons may only link to these route names. Free-form URLs are not
    | accepted from the admin panel.
    |
    */

    'cta_targets' => [
        'services' => 'services.index',
        'contact' => 'contact',
        'about' => 'about',
        'profile' => 'profile.download',
    ],

    /*
    |--------------------------------------------------------------------------
    | Uploads
    |--------------------------------------------------------------------------
    */

    'images' => [
        'widths' => [480, 768, 1280, 1920, 2560],
        'fallback_width' => 1280,
        'webp_quality' => 80,
        'jpeg_quality' => 82,
        'max_kilobytes' => 8192,
        'max_pixels' => 40_000_000,
        'min_width' => 800,
    ],

    'pdf' => [
        'max_kilobytes' => 15360,
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact form
    |--------------------------------------------------------------------------
    */

    'contact' => [
        'notify' => (bool) env('CONTACT_NOTIFY', false),
        'min_seconds' => 3,
        'max_age_seconds' => 7200,
        'per_minute' => 3,
        'per_day' => 20,
    ],

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
    ],

];
