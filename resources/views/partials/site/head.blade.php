@php
    use App\Support\LocaleUrl;

    $locales = config('site.locales');
    $title = $seo->documentTitle();
    $description = $seo->getDescription();
    $canonical = LocaleUrl::canonical();
@endphp
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title }}</title>
@if ($description)
    <meta name="description" content="{{ $description }}">
@endif
@unless (app()->isProduction())
    <meta name="robots" content="noindex, nofollow">
@endunless
<link rel="canonical" href="{{ $canonical }}">
@foreach (array_keys($locales) as $alternateLocale)
    <link rel="alternate" hreflang="{{ $alternateLocale }}" href="{{ LocaleUrl::alternate($alternateLocale) }}">
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ LocaleUrl::alternate(config('site.default_locale')) }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ site('short_name') ?? config('app.name') }}">
<meta property="og:title" content="{{ $title }}">
@if ($description)
    <meta property="og:description" content="{{ $description }}">
@endif
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:locale" content="{{ $locales[$locale]['og'] }}">
@foreach ($locales as $code => $info)
    @if ($code !== $locale)
        <meta property="og:locale:alternate" content="{{ $info['og'] }}">
    @endif
@endforeach
<meta property="og:image" content="{{ $seo->imageUrl() }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
@if ($description)
    <meta name="twitter:description" content="{{ $description }}">
@endif
<meta name="twitter:image" content="{{ $seo->imageUrl() }}">

<meta name="theme-color" content="#52413B">
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

@vite(['resources/css/app.css', 'resources/js/app.js'])

{{ app(\App\Services\Seo\JsonLd::class)->script() }}
