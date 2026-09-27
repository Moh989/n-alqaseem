@extends('layouts.site')

@section('body_class', 'page-service')

@section('content')
    <x-page-hero
        :title="$service->tr('title')"
        :eyebrow="$service->category?->tr('name')"
        :lead="$service->tr('summary')"
        :crumbs="[
            ['label' => __('site.nav.services'), 'url' => lroute('services.index')],
            ['label' => $service->tr('title')],
        ]"
    />

    <div class="section">
        <div class="container service-layout">
            <article class="service-layout__main">
                @if ($service->media)
                    <figure class="service-figure">
                        <x-picture :media="$service->media" sizes="(min-width: 1100px) 760px, 92vw" :eager="true" img-class="service-figure__img" />
                    </figure>
                @endif
                <x-prose :text="$service->tr('body')" class="prose--lg" />
            </article>

            <aside class="service-layout__aside">
                <div class="aside-card aside-card--dark">
                    <x-icon :name="$service->icon ?? 'building-2'" class="aside-card__icon" />
                    <h2 class="aside-card__title">{{ __('site.services.cta_title') }}</h2>
                    <p>{{ __('site.services.cta_text') }}</p>
                    <a class="btn btn--light btn--block" href="{{ lroute('contact', ['type' => 'quote']) }}">{{ __('site.actions.request_quote') }} <x-icon name="arrow-right" class="icon--dir" /></a>
                    @if ($email = site('email'))
                        <a class="aside-card__email" href="mailto:{{ $email }}"><x-icon name="mail" /> <bdi dir="ltr">{{ $email }}</bdi></a>
                    @endif
                </div>

                @if ($related->isNotEmpty())
                    <nav class="aside-card" aria-labelledby="related-services">
                        <h2 class="aside-card__title" id="related-services">{{ __('site.services.related') }}</h2>
                        <ul class="related-list">
                            @foreach ($related as $item)
                                <li>
                                    <a href="{{ $item->url() }}">
                                        <x-icon :name="$item->icon ?? 'arrow-right'" />
                                        <span>{{ $item->tr('title') }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <a class="link-arrow" href="{{ lroute('services.index') }}">{{ __('site.actions.all_services') }} <x-icon name="arrow-right" class="icon--dir" /></a>
                    </nav>
                @endif
            </aside>
        </div>
    </div>
@endsection
