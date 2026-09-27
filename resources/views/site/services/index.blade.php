@extends('layouts.site')

@section('body_class', 'page-services')

@section('content')
    <x-slider :slides="$slides" :eyebrow="__('site.services.eyebrow')" />

    <section class="section intro intro--compact" aria-labelledby="services-title">
        <div class="container">
            <p class="eyebrow">{{ __('site.services.eyebrow') }}</p>
            <h1 class="intro__title" id="services-title">{{ $intro?->tr('title') ?? __('site.nav.services') }}</h1>
            @if ($intro)
                <x-prose :text="$intro->tr('body')" class="intro__body intro__body--wide" />
            @endif

            @if ($categories->count() > 1)
                <nav class="category-nav" aria-label="{{ __('site.services.categories') }}">
                    <ul>
                        @foreach ($categories as $category)
                            <li><a href="#category-{{ $category->slug }}">{{ $category->tr('name') }} <span>{{ $category->publishedServices->count() }}</span></a></li>
                        @endforeach
                    </ul>
                </nav>
            @endif
        </div>
    </section>

    @foreach ($categories as $category)
        <section class="section {{ $loop->odd ? 'section--tint' : '' }}" id="category-{{ $category->slug }}" aria-labelledby="category-{{ $category->slug }}-title">
            <div class="container">
                <x-section-heading :title="$category->tr('name')" :lead="$category->tr('description')" id="category-{{ $category->slug }}-title" />
                <div class="card-grid">
                    @foreach ($category->publishedServices as $service)
                        <x-service-card :service="$service" />
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach

    <x-cta-band :title="__('site.services.cta_title')" :text="__('site.services.cta_text')" />
@endsection
