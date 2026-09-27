<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}">
<head>
    @include('partials.site.head')
</head>
<body class="site site--{{ $dir }} @yield('body_class')">
    <a class="skip-link" href="#main">{{ __('site.skip_to_content') }}</a>

    @include('partials.site.header')

    <main id="main" tabindex="-1">
        @yield('content')
    </main>

    @include('partials.site.footer')
</body>
</html>
