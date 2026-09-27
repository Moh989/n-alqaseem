@extends('layouts.site')

@section('body_class', 'page-legal')

@section('content')
    <x-page-hero
        :title="__('site.footer.'.$page)"
        :crumbs="[['label' => __('site.footer.'.$page)]]"
    >
        @if ($updatedAt)
            <p class="page-hero__meta">{{ __('site.legal.last_updated', ['date' => $updatedAt->locale($locale)->translatedFormat('j F Y')]) }}</p>
        @endif
    </x-page-hero>

    <div class="section">
        <div class="container legal">
            @if ($sections->count() > 2)
                <nav class="legal__toc" aria-label="{{ $locale === 'ar' ? 'محتويات الصفحة' : 'On this page' }}">
                    <ol>
                        @foreach ($sections as $section)
                            @if ($title = $section->tr('title'))
                                <li><a href="#{{ $section->key }}">{{ $title }}</a></li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            @endif
            <div class="legal__body">
                @foreach ($sections as $section)
                    <section class="legal__section" id="{{ $section->key }}">
                        @if ($title = $section->tr('title'))
                            <h2>{{ $title }}</h2>
                        @endif
                        <x-prose :text="$section->tr('body')" :heading-level="3" />
                    </section>
                @endforeach
            </div>
        </div>
    </div>
@endsection
