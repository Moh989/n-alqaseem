@php
    $legal = $site->legalName();
    $brandName = $site->get('short_name') ?? config('app.name');
@endphp
<footer class="site-footer">
    <x-chevrons class="site-footer__band" />
    <div class="container site-footer__grid">
        <div class="site-footer__about">
            <a class="brand brand--light" href="{{ lroute('home') }}">
                <x-brand-mark class="brand__mark" />
                <span class="brand__name">{{ $brandName }}</span>
            </a>
            @if ($legal['name'])
                <p class="site-footer__legal" lang="{{ $legal['lang'] }}" dir="{{ $legal['lang'] === 'ar' ? 'rtl' : 'ltr' }}">{{ $legal['name'] }}</p>
            @endif
            @if ($type = $site->get('company_type'))
                <p class="site-footer__type">{{ $type }}</p>
            @endif
        </div>

        <nav class="site-footer__col" aria-labelledby="footer-links">
            <h2 class="site-footer__heading" id="footer-links">{{ __('site.footer.links') }}</h2>
            <ul>
                <li><a href="{{ lroute('about') }}">{{ __('site.nav.about') }}</a></li>
                <li><x-profile-link>{{ __('site.nav.profile') }}</x-profile-link></li>
                <li><a href="{{ lroute('services.index') }}">{{ __('site.nav.services') }}</a></li>
                <li><a href="{{ lroute('contact') }}">{{ __('site.nav.contact') }}</a></li>
            </ul>
        </nav>

        <nav class="site-footer__col site-footer__services" aria-labelledby="footer-services">
            <h2 class="site-footer__heading" id="footer-services">{{ __('site.footer.services') }}</h2>
            <ul>
                @foreach ($navCategories->flatMap->publishedServices as $footerService)
                    <li><a href="{{ $footerService->url() }}">{{ $footerService->tr('title') }}</a></li>
                @endforeach
            </ul>
        </nav>

        <div class="site-footer__col">
            <h2 class="site-footer__heading">{{ __('site.footer.contact') }}</h2>
            <ul class="contact-list contact-list--light">
                @if ($address = $site->get('address'))
                    <li><x-icon name="map-pin" /> <span>{{ $address }}</span></li>
                @endif
                @if ($email = $site->get('email'))
                    <li><x-icon name="mail" /> <a href="mailto:{{ $email }}"><bdi dir="ltr">{{ $email }}</bdi></a></li>
                @endif
            </ul>
        </div>
    </div>

    <div class="container site-footer__bottom">
        <p>{{ __('site.footer.rights', ['year' => now()->year, 'name' => $brandName]) }}</p>
        <ul class="site-footer__legal-links">
            <li><a href="{{ lroute('privacy') }}">{{ __('site.footer.privacy') }}</a></li>
            <li><a href="{{ lroute('terms') }}">{{ __('site.footer.terms') }}</a></li>
        </ul>
    </div>
</footer>
