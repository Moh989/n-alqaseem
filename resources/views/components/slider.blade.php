@props(['slides', 'eyebrow' => null, 'variant' => 'page', 'label' => null])
@php($total = $slides->count())
@if ($total > 0)
    <section {{ $attributes->class(['hero', 'hero--'.$variant, 'hero--single' => $total === 1]) }}
             data-slider data-parallax
             aria-roledescription="carousel"
             aria-label="{{ $label ?? __('site.slider.label') }}">
        <div class="hero__viewport" data-slider-track aria-live="off">
            @foreach ($slides as $index => $slide)
                <div class="hero__slide{{ $index === 0 ? ' is-active' : '' }}"
                     role="group"
                     aria-roledescription="{{ __('site.slider.slide') }}"
                     aria-label="{{ __('site.slider.slide_of', ['current' => $index + 1, 'total' => $total]) }}"
                     data-slide
                     @if ($index > 0) inert @endif>
                    <div class="hero__media" data-depth="18">
                        @if ($slide->media)
                            <x-picture :media="$slide->media" :eager="$index === 0" alt="" img-class="hero__img" />
                        @else
                            <div class="hero__placeholder"></div>
                        @endif
                    </div>
                    <div class="hero__scrim"></div>
                    <div class="hero__deco" data-depth="36" aria-hidden="true">
                        <x-brand-mark class="hero__deco-mark" />
                    </div>
                    <div class="container hero__content" data-depth="-8">
                        @if ($eyebrow)
                            <p class="hero__eyebrow">{{ $eyebrow }}</p>
                        @endif
                        <h2 class="hero__title">{{ $slide->tr('heading') }}</h2>
                        @if ($text = $slide->tr('text'))
                            <p class="hero__text">{{ $text }}</p>
                        @endif
                        @if ($ctas = $slide->ctas())
                            <div class="hero__actions">
                                @foreach ($ctas as $ctaIndex => $cta)
                                    <a class="btn {{ $ctaIndex === 0 ? 'btn--light' : 'btn--outline-light' }}" href="{{ $cta['url'] }}"
                                       @if ($cta['new_tab']) target="_blank" rel="noopener" type="application/pdf" @endif>
                                        {{ $cta['label'] }}
                                        @if ($cta['new_tab'])<span class="visually-hidden"> {{ __('site.actions.opens_new_tab') }}</span>@endif
                                        @if ($ctaIndex === 0) <x-icon name="arrow-right" class="icon--dir" /> @endif
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if ($total > 1)
            <div class="container hero__controls">
                <button class="slider-btn slider-btn--toggle" type="button" data-slider-toggle
                        data-label-pause="{{ __('site.slider.pause') }}" data-label-play="{{ __('site.slider.play') }}"
                        aria-label="{{ __('site.slider.pause') }}">
                    <x-icon name="pause" class="slider-btn__pause" />
                    <x-icon name="play" class="slider-btn__play" />
                </button>
                <button class="slider-btn" type="button" data-slider-prev aria-label="{{ __('site.slider.previous') }}">
                    <x-icon name="arrow-right" class="icon--dir icon--flip" />
                </button>
                <ol class="hero__dots">
                    @foreach ($slides as $index => $slide)
                        <li>
                            <button class="hero__dot" type="button" data-slider-dot="{{ $index }}"
                                    aria-label="{{ __('site.slider.go_to', ['number' => $index + 1]) }}"
                                    @if ($index === 0) aria-current="true" @endif></button>
                        </li>
                    @endforeach
                </ol>
                <button class="slider-btn" type="button" data-slider-next aria-label="{{ __('site.slider.next') }}">
                    <x-icon name="arrow-right" class="icon--dir" />
                </button>
                <p class="hero__counter" aria-hidden="true" dir="ltr"><span data-slider-current>01</span> / {{ str_pad((string) $total, 2, '0', STR_PAD_LEFT) }}</p>
            </div>
        @endif
    </section>
@endif
