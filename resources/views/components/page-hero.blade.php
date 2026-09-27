@props(['title', 'eyebrow' => null, 'lead' => null, 'crumbs' => []])
<header {{ $attributes->class(['page-hero']) }}>
    <div class="page-hero__deco" aria-hidden="true"><x-brand-mark class="page-hero__mark" /></div>
    <div class="container page-hero__inner">
        @if ($crumbs)
            <nav class="breadcrumbs" aria-label="{{ app()->getLocale() === 'ar' ? 'مسار التنقل' : 'Breadcrumb' }}">
                <ol>
                    <li><a href="{{ lroute('home') }}">{{ __('site.nav.home') }}</a></li>
                    @foreach ($crumbs as $crumb)
                        <li>
                            @if (! empty($crumb['url']))
                                <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                            @else
                                <span aria-current="page">{{ $crumb['label'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif
        @if ($eyebrow)
            <p class="eyebrow eyebrow--light">{{ $eyebrow }}</p>
        @endif
        <h1 class="page-hero__title">{{ $title }}</h1>
        @if ($lead)
            <p class="page-hero__lead">{{ $lead }}</p>
        @endif
        {{ $slot }}
    </div>
    <x-chevrons class="page-hero__band" />
</header>
