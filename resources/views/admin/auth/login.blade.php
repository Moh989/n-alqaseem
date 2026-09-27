<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ __('admin.auth.title') }} — {{ __('admin.title') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="admin admin--auth">
    <main class="auth-card">
        <x-brand-mark class="auth-card__mark" />
        <h1 class="auth-card__title">{{ __('admin.auth.title') }}</h1>
        <p class="auth-card__subtitle">{{ __('admin.title') }} — {{ site('short_name', 'ar') ?? config('app.name') }}</p>

        @if ($errors->any())
            <div class="notice notice--error" role="alert"><x-icon name="circle-alert" /> {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}" class="stack">
            @csrf
            <div class="a-field">
                <label class="a-field__label" for="email">{{ __('admin.auth.email') }}</label>
                <input class="a-input" id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" dir="ltr" autofocus>
            </div>
            <div class="a-field">
                <label class="a-field__label" for="password">{{ __('admin.auth.password') }}</label>
                <input class="a-input" id="password" name="password" type="password" required autocomplete="current-password" dir="ltr">
            </div>
            <label class="checkbox">
                <input type="checkbox" name="remember" value="1"> <span>{{ __('admin.auth.remember') }}</span>
            </label>
            <button class="btn btn--primary btn--block" type="submit">{{ __('admin.auth.submit') }}</button>
        </form>
        <a class="auth-card__back" href="{{ url('/') }}">{{ __('admin.nav.view_site') }}</a>
    </main>
</body>
</html>
