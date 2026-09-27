@props(['service', 'headingLevel' => 3, 'sizes' => '(min-width: 1100px) 360px, (min-width: 700px) 45vw, 92vw'])
<article {{ $attributes->class(['service-card']) }}>
    <div class="service-card__media">
        @if ($service->media)
            <x-picture :media="$service->media" :sizes="$sizes" alt="" img-class="service-card__img" />
        @else
            <div class="service-card__placeholder" aria-hidden="true"><x-brand-mark /></div>
        @endif
        <span class="service-card__icon" aria-hidden="true"><x-icon :name="$service->icon ?? 'building-2'" /></span>
    </div>
    <div class="service-card__body">
        <h{{ $headingLevel }} class="service-card__title">
            <a class="stretched-link" href="{{ $service->url() }}">{{ $service->tr('title') }}</a>
        </h{{ $headingLevel }}>
        @if ($summary = $service->tr('summary'))
            <p class="service-card__summary">{{ $summary }}</p>
        @endif
        <span class="service-card__more" aria-hidden="true">{{ __('site.actions.read_more') }} <x-icon name="arrow-right" class="icon--dir" /></span>
    </div>
</article>
