{{-- Standalone error page: no database access, so it renders even when the app is failing. --}}
@php($locale = app()->getLocale() === 'en' ? 'en' : 'ar')
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ __("site.errors.{$code}_title", [], $locale) }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css'])
</head>
<body class="site page-error">
    <main class="error-page error-page--standalone">
        <div class="container error-page__inner">
            <img class="error-page__logo" src="{{ asset('images/logo.svg') }}" alt="Noor AlQaseem" width="120" height="86">
            <p class="error-page__code" aria-hidden="true">{{ $code }}</p>
            <h1 class="error-page__title">{{ __("site.errors.{$code}_title", [], $locale) }}</h1>
            <p class="error-page__text">{{ __("site.errors.{$code}_text", [], $locale) }}</p>
            <div class="error-page__actions">
                <a class="btn btn--primary" href="{{ url($locale === 'en' ? '/en' : '/') }}">{{ __('site.actions.back_home', [], $locale) }}</a>
            </div>
        </div>
    </main>
</body>
</html>
