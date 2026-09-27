@props(['eyebrow' => null, 'title', 'lead' => null, 'level' => 2, 'id' => null, 'align' => 'start'])
<div {{ $attributes->class(['section-heading', 'section-heading--center' => $align === 'center']) }}>
    @if ($eyebrow)
        <p class="eyebrow">{{ $eyebrow }}</p>
    @endif
    <h{{ $level }} class="section-heading__title" @if ($id) id="{{ $id }}" @endif>{{ $title }}</h{{ $level }}>
    @if ($lead)
        <p class="section-heading__lead">{{ $lead }}</p>
    @endif
</div>
