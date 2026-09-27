<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') — {{ __('admin.title') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="admin">
    <a class="skip-link" href="#admin-main">{{ __('site.skip_to_content', [], 'ar') }}</a>
    @php
        $unread = \App\Models\ContactMessage::whereNull('read_at')->whereNull('archived_at')->count();
        $nav = [
            ['admin.dashboard', 'layout-dashboard', __('admin.nav.dashboard'), 'admin.dashboard'],
            ['admin.settings.index', 'settings', __('admin.nav.settings'), 'admin.settings.*'],
            ['admin.pages.index', 'file-text', __('admin.nav.pages'), 'admin.pages.*'],
            ['admin.slides.index', 'images', __('admin.nav.slides'), 'admin.slides.*'],
            ['admin.categories.index', 'folder-tree', __('admin.nav.categories'), 'admin.categories.*'],
            ['admin.services.index', 'briefcase', __('admin.nav.services'), 'admin.services.*'],
            ['admin.seo.index', 'search', __('admin.nav.seo'), 'admin.seo.*'],
            ['admin.profile-pdf.index', 'download', __('admin.nav.profile_pdf'), 'admin.profile-pdf.*'],
            ['admin.messages.index', 'inbox', __('admin.nav.messages'), 'admin.messages.*'],
            ['admin.account.edit', 'user', __('admin.nav.account'), 'admin.account.*'],
        ];
    @endphp
    <div class="admin-shell">
        <aside class="admin-sidebar" id="admin-sidebar" data-admin-sidebar>
            <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                <x-brand-mark class="admin-brand__mark" />
                <span>{{ __('admin.title') }}</span>
            </a>
            <nav aria-label="{{ __('admin.title') }}">
                <ul class="admin-nav">
                    @foreach ($nav as [$route, $icon, $label, $pattern])
                        <li>
                            <a href="{{ route($route) }}" @if (request()->routeIs($pattern)) aria-current="page" @endif>
                                <x-icon :name="$icon" /> <span>{{ $label }}</span>
                                @if ($route === 'admin.messages.index' && $unread > 0)
                                    <span class="admin-nav__count" aria-label="{{ $unread }} {{ __('admin.messages.new') }}">{{ $unread }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
            <a class="admin-sidebar__site" href="{{ url('/') }}" target="_blank" rel="noopener"><x-icon name="external-link" /> {{ __('admin.nav.view_site') }}</a>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <button class="admin-topbar__menu" type="button" aria-controls="admin-sidebar" aria-expanded="false" data-admin-menu>
                    <x-icon name="menu" /><span class="visually-hidden">{{ __('admin.nav.open_menu') }}</span>
                </button>
                <p class="admin-topbar__title">@yield('title')</p>
                <div class="admin-topbar__user">
                    <span>{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="btn btn--ghost btn--sm" type="submit"><x-icon name="log-out" /> {{ __('admin.auth.logout') }}</button>
                    </form>
                </div>
            </header>

            <main class="admin-content" id="admin-main" tabindex="-1">
                <x-admin.flash />
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
