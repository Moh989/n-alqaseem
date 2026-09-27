@php
    use App\Support\LocaleUrl;

    $otherLocale = $locale === 'ar' ? 'en' : 'ar';
    $current = request()->route()?->getName() ? LocaleUrl::baseName(request()->route()->getName()) : null;
    $isActive = fn (string ...$names): bool => in_array($current, $names, true);
    $brandName = $site->get('short_name') ?? config('app.name');
    $currentService = request()->route('service');
    $currentService = $currentService instanceof \App\Models\Service ? $currentService : null;
@endphp
<header class="site-header" data-header>
    <x-chevrons class="site-header__band" />
    <div class="container site-header__inner">
        <a class="brand" href="{{ lroute('home') }}" @if ($isActive('home')) aria-current="page" @endif>
            <x-brand-mark class="brand__mark" />
            <span class="brand__text">
                <span class="brand__name">{{ $brandName }}</span>
                <span class="brand__tagline">{{ $locale === 'ar' ? 'للتجارة والمقاولات العامة' : 'General Trading & Contracting' }}</span>
            </span>
        </a>

        <nav class="primary-nav" aria-label="{{ __('site.nav.label') }}">
            <ul class="primary-nav__list">
                <li><a class="primary-nav__link" href="{{ lroute('home') }}" @if ($isActive('home')) aria-current="page" @endif>{{ __('site.nav.home') }}</a></li>
                <li><a class="primary-nav__link" href="{{ lroute('about') }}" @if ($isActive('about')) aria-current="page" @endif>{{ __('site.nav.about') }}</a></li>
                <li class="primary-nav__item has-dropdown" data-dropdown>
                    <a class="primary-nav__link" href="{{ lroute('services.index') }}" @if ($isActive('services.index', 'services.show')) aria-current="{{ $isActive('services.index') ? 'page' : 'true' }}" @endif>{{ __('site.nav.services') }}</a>
                    <button class="dropdown-toggle" type="button" aria-expanded="false" aria-controls="services-menu" data-dropdown-toggle>
                        <x-icon name="chevron-down" />
                        <span class="visually-hidden">{{ __('site.nav.services_submenu') }}</span>
                    </button>
                    <div class="dropdown" id="services-menu" data-dropdown-panel hidden>
                        <div class="dropdown__grid">
                            @foreach ($navCategories as $category)
                                <div class="dropdown__group">
                                    <p class="dropdown__title">{{ $category->tr('name') }}</p>
                                    <ul class="dropdown__list">
                                        @foreach ($category->publishedServices as $navService)
                                            <li>
                                                <a class="dropdown__link" href="{{ $navService->url() }}" @if ($currentService?->is($navService)) aria-current="page" @endif>
                                                    <x-icon :name="$navService->icon ?? 'arrow-right'" />
                                                    <span>{{ $navService->tr('title') }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                        <a class="dropdown__all" href="{{ lroute('services.index') }}">
                            {{ __('site.nav.all_services') }} <x-icon name="arrow-right" class="icon--dir" />
                        </a>
                    </div>
                </li>
                <li><a class="primary-nav__link" href="{{ lroute('contact') }}" @if ($isActive('contact')) aria-current="page" @endif>{{ __('site.nav.contact') }}</a></li>
            </ul>
        </nav>

        <div class="site-header__actions">
            <a class="lang-switch" href="{{ LocaleUrl::alternate($otherLocale) }}" hreflang="{{ $otherLocale }}" lang="{{ $otherLocale }}" aria-label="{{ __('site.nav.switch_language_label') }}">
                <x-icon name="globe" />
                <span>{{ __('site.nav.switch_language') }}</span>
            </a>
            <x-profile-link class="btn btn--primary btn--sm site-header__cta">
                <x-icon name="file-text" /> {{ __('site.nav.profile') }}
            </x-profile-link>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="offcanvas" data-offcanvas-open>
                <x-icon name="menu" />
                <span class="visually-hidden">{{ __('site.nav.open_menu') }}</span>
            </button>
        </div>
    </div>
</header>

<div class="offcanvas" id="offcanvas" data-offcanvas hidden>
    <div class="offcanvas__backdrop" data-offcanvas-close></div>
    <div class="offcanvas__panel" role="dialog" aria-modal="true" aria-label="{{ __('site.nav.menu') }}" tabindex="-1">
        <div class="offcanvas__head">
            <a class="brand brand--compact" href="{{ lroute('home') }}">
                <x-brand-mark class="brand__mark" />
                <span class="brand__name">{{ $brandName }}</span>
            </a>
            <button class="offcanvas__close" type="button" data-offcanvas-close>
                <x-icon name="x" />
                <span class="visually-hidden">{{ __('site.nav.close_menu') }}</span>
            </button>
        </div>
        <nav aria-label="{{ __('site.nav.label') }}">
            <ul class="mobile-nav">
                <li><a class="mobile-nav__link" href="{{ lroute('home') }}" @if ($isActive('home')) aria-current="page" @endif>{{ __('site.nav.home') }}</a></li>
                <li><a class="mobile-nav__link" href="{{ lroute('about') }}" @if ($isActive('about')) aria-current="page" @endif>{{ __('site.nav.about') }}</a></li>
                <li class="mobile-nav__group" data-disclosure>
                    <div class="mobile-nav__row">
                        <a class="mobile-nav__link" href="{{ lroute('services.index') }}" @if ($isActive('services.index')) aria-current="page" @endif>{{ __('site.nav.services') }}</a>
                        <button class="mobile-nav__toggle" type="button" aria-expanded="false" aria-controls="mobile-services" data-disclosure-toggle>
                            <x-icon name="chevron-down" />
                            <span class="visually-hidden">{{ __('site.nav.services_submenu') }}</span>
                        </button>
                    </div>
                    <div class="mobile-nav__sub" id="mobile-services" hidden>
                        @foreach ($navCategories as $category)
                            <p class="mobile-nav__title">{{ $category->tr('name') }}</p>
                            <ul>
                                @foreach ($category->publishedServices as $navService)
                                    <li><a href="{{ $navService->url() }}">{{ $navService->tr('title') }}</a></li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>
                </li>
                <li><a class="mobile-nav__link" href="{{ lroute('contact') }}" @if ($isActive('contact')) aria-current="page" @endif>{{ __('site.nav.contact') }}</a></li>
            </ul>
        </nav>
        <div class="offcanvas__foot">
            <x-profile-link class="btn btn--primary btn--block"><x-icon name="file-text" /> {{ __('site.actions.view_profile') }}</x-profile-link>
            <a class="lang-switch lang-switch--block" href="{{ LocaleUrl::alternate($otherLocale) }}" hreflang="{{ $otherLocale }}" lang="{{ $otherLocale }}">
                <x-icon name="globe" /> <span>{{ __('site.nav.switch_language') }}</span>
            </a>
            @if ($email = $site->get('email'))
                <a class="offcanvas__contact" href="mailto:{{ $email }}"><x-icon name="mail" /> <bdi dir="ltr">{{ $email }}</bdi></a>
            @endif
        </div>
    </div>
</div>
