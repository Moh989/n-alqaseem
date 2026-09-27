@extends('layouts.site')

@section('body_class', 'page-home')

@section('content')
    <x-slider :slides="$slides" variant="home" :eyebrow="site('short_name')" />

    @php($intro = $sections->get('intro'))
    <section class="section intro" aria-labelledby="home-title">
        <div class="container intro__grid">
            <div class="intro__copy">
                <p class="eyebrow">{{ __('site.home.about_eyebrow') }}</p>
                <h1 class="intro__title" id="home-title">{{ $intro?->tr('title') ?? site('short_name') }}</h1>
                @if ($intro)
                    <x-prose :text="$intro->tr('body')" class="intro__body" />
                @endif
                <div class="intro__actions">
                    <x-profile-link class="btn btn--primary"><x-icon name="file-text" /> {{ __('site.actions.view_profile') }}</x-profile-link>
                    @if ($hasProfilePdf)
                        <a class="btn btn--outline" href="{{ lroute('about') }}">{{ __('site.actions.about_more') }} <x-icon name="arrow-right" class="icon--dir" /></a>
                    @endif
                </div>
            </div>
            <aside class="facts" aria-label="{{ __('site.home.about_eyebrow') }}">
                <x-brand-mark class="facts__mark" />
                <dl class="facts__list">
                    @if ($type = site('company_type'))
                        <div class="facts__item">
                            <dt>{{ __('site.home.company_type') }}</dt>
                            <dd>{{ $type }}</dd>
                        </div>
                    @endif
                    @if ($address = site('address'))
                        <div class="facts__item">
                            <dt>{{ __('site.home.headquarters') }}</dt>
                            <dd>{{ $address }}</dd>
                        </div>
                    @endif
                    @if ($year = site('founding_year'))
                        <div class="facts__item">
                            <dt>{{ __('site.home.founded') }}</dt>
                            <dd>{{ $year }}</dd>
                        </div>
                    @endif
                </dl>
            </aside>
        </div>
    </section>

    @if ($categories->isNotEmpty())
        @php($servicesSection = $sections->get('services'))
        <section class="section section--tint" aria-labelledby="home-services">
            <div class="container">
                <div class="section__head">
                    <x-section-heading :eyebrow="__('site.home.services_eyebrow')" :title="$servicesSection?->tr('title') ?? __('site.nav.services')" :lead="$servicesSection?->tr('body')" id="home-services" />
                    <a class="link-arrow" href="{{ lroute('services.index') }}">{{ __('site.actions.all_services') }} <x-icon name="arrow-right" class="icon--dir" /></a>
                </div>

                <div class="category-tabs">
                    @foreach ($categories as $category)
                        <div class="category-block">
                            <h3 class="category-block__title">
                                <span>{{ $category->tr('name') }}</span>
                                <small>{{ trans_choice('site.services.count', $category->publishedServices->count(), ['count' => $category->publishedServices->count()]) }}</small>
                            </h3>
                            <ul class="service-chips">
                                @foreach ($category->publishedServices as $service)
                                    <li>
                                        <a class="service-chip" href="{{ $service->url() }}">
                                            <span class="service-chip__icon"><x-icon :name="$service->icon ?? 'building-2'" /></span>
                                            <span class="service-chip__text">
                                                <strong>{{ $service->tr('title') }}</strong>
                                                <span>{{ $service->tr('summary') }}</span>
                                            </span>
                                            <x-icon name="arrow-right" class="icon--dir service-chip__arrow" />
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($process && $process->items->isNotEmpty())
        @php($processSection = $sections->get('process'))
        <section class="section section--dark" aria-labelledby="home-process">
            <div class="container">
                <x-section-heading class="section-heading--light" :eyebrow="__('site.home.process_eyebrow')" :title="$processSection?->tr('title') ?? $process->tr('title')" :lead="$processSection?->tr('body') ?? $process->tr('body')" id="home-process" />
                <ol class="steps steps--light">
                    @foreach ($process->items as $step)
                        <li class="step">
                            <span class="step__num" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <x-icon :name="$step->icon ?? 'flag'" class="step__icon" />
                            <h3 class="step__title">{{ $step->tr('title') }}</h3>
                            @if ($text = $step->tr('text'))
                                <p class="step__text">{{ $text }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    @php($cta = $sections->get('cta'))
    <x-cta-band :title="$cta?->tr('title') ?? __('site.nav.contact')" :text="$cta?->tr('body')" />
@endsection
