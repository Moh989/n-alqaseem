@extends('layouts.site')

@section('body_class', 'page-about')

@section('content')
    <x-slider :slides="$slides" :eyebrow="__('site.about.eyebrow')" />

    @php($intro = $sections->get('intro'))
    <section class="section intro" aria-labelledby="about-title">
        <div class="container intro__grid">
            <div class="intro__copy">
                <p class="eyebrow">{{ __('site.about.eyebrow') }}</p>
                <h1 class="intro__title" id="about-title">{{ $intro?->tr('title') ?? __('site.nav.about') }}</h1>
                @if ($intro)
                    <x-prose :text="$intro->tr('body')" class="intro__body" />
                @endif
                @if ($hasProfilePdf)
                    <div class="intro__actions">
                        <x-profile-link class="btn btn--primary"><x-icon name="file-text" /> {{ __('site.actions.view_profile') }}</x-profile-link>
                    </div>
                @endif
            </div>
            @if ($context = $sections->get('context'))
                <aside class="callout" aria-labelledby="about-context">
                    <x-brand-mark class="callout__mark" />
                    <h2 class="callout__title" id="about-context">{{ $context->tr('title') }}</h2>
                    <x-prose :text="$context->tr('body')" />
                </aside>
            @endif
        </div>
    </section>

    @if ($sections->has('mission') || $sections->has('vision'))
        <section class="section section--tint" aria-label="{{ $locale === 'ar' ? 'الرسالة والرؤية' : 'Mission and vision' }}">
            <div class="container mv-grid">
                @foreach (['mission' => 'flag', 'vision' => 'search'] as $key => $icon)
                    @if ($section = $sections->get($key))
                        <article class="mv-card">
                            <span class="mv-card__icon"><x-icon :name="$icon" /></span>
                            <h2 class="mv-card__title">{{ $section->tr('title') }}</h2>
                            <x-prose :text="$section->tr('body')" />
                        </article>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    @if (($values = $sections->get('values')) && $values->items->isNotEmpty())
        <section class="section" aria-labelledby="about-values">
            <div class="container">
                <x-section-heading :title="$values->tr('title')" :lead="$values->tr('body')" id="about-values" />
                <ul class="value-grid">
                    @foreach ($values->items as $value)
                        <li class="value-card">
                            <span class="value-card__icon"><x-icon :name="$value->icon ?? 'badge-check'" /></span>
                            <h3 class="value-card__title">{{ $value->tr('title') }}</h3>
                            @if ($text = $value->tr('text'))
                                <p>{{ $text }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if (($experience = $sections->get('experience')) || ($scope = $sections->get('scope')))
        <section class="section section--tint" aria-labelledby="about-scope">
            <div class="container split">
                @if ($experience = $sections->get('experience'))
                    <div class="split__aside">
                        <x-section-heading :title="$experience->tr('title')" />
                        <x-prose :text="$experience->tr('body')" />
                    </div>
                @endif
                @if ($scope = $sections->get('scope'))
                    <div class="split__main">
                        <h2 class="h3" id="about-scope">{{ $scope->tr('title') }}</h2>
                        <ul class="scope-list">
                            @foreach ($scope->items as $item)
                                <li class="scope-item">
                                    <span class="scope-item__icon"><x-icon :name="$item->icon ?? 'hard-hat'" /></span>
                                    <div>
                                        <h3 class="scope-item__title">{{ $item->tr('title') }}</h3>
                                        @if ($text = $item->tr('text'))
                                            <p>{{ $text }}</p>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        @if ($note = $scope->tr('body'))
                            <p class="note">{{ $note }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if (($stages = $sections->get('stages')) && $stages->items->isNotEmpty())
        <section class="section" aria-labelledby="about-stages">
            <div class="container">
                <x-section-heading :title="$stages->tr('title')" :lead="$stages->tr('body')" id="about-stages" />
                <ol class="timeline">
                    @foreach ($stages->items as $stage)
                        <li class="timeline__item">
                            <span class="timeline__num" aria-hidden="true">{{ $loop->iteration }}</span>
                            <div class="timeline__body">
                                <h3 class="timeline__title"><x-icon :name="$stage->icon ?? 'flag'" /> {{ $stage->tr('title') }}</h3>
                                @if ($text = $stage->tr('text'))
                                    <p>{{ $text }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    @if (($process = $sections->get('process')) && $process->items->isNotEmpty())
        <section class="section section--dark" aria-labelledby="about-process">
            <div class="container">
                <x-section-heading class="section-heading--light" :title="$process->tr('title')" :lead="$process->tr('body')" id="about-process" />
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

    <section class="section" aria-label="{{ $locale === 'ar' ? 'الجودة والهيكل التنظيمي' : 'Quality and organisation' }}">
        <div class="container split">
            @if ($quality = $sections->get('quality'))
                <div class="split__main">
                    <x-section-heading :title="$quality->tr('title')" />
                    <x-prose :text="$quality->tr('body')" />
                </div>
            @endif
            @if (($structure = $sections->get('structure')) && $structure->items->isNotEmpty())
                <div class="split__aside">
                    <div class="org-chart">
                        <h2 class="h3">{{ $structure->tr('title') }}</h2>
                        @if ($body = $structure->tr('body'))
                            <p>{{ $body }}</p>
                        @endif
                        <p class="org-chart__head">{{ __('site.about.director') }}</p>
                        <ul class="org-chart__list" aria-label="{{ __('site.about.departments') }}">
                            @foreach ($structure->items as $department)
                                <li><x-icon :name="$department->icon ?? 'folder-cog'" /> {{ $department->tr('title') }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <x-cta-band :title="__('site.services.cta_title')" :text="__('site.services.cta_text')" />
@endsection
