@props(['title', 'text' => null])
<section {{ $attributes->class(['cta-band']) }} aria-labelledby="cta-title">
    <div class="cta-band__deco" aria-hidden="true"><x-brand-mark class="cta-band__mark" /></div>
    <div class="container cta-band__inner">
        <div class="cta-band__copy">
            <h2 class="cta-band__title" id="cta-title">{{ $title }}</h2>
            @if ($text)
                <p class="cta-band__text">{{ $text }}</p>
            @endif
        </div>
        <div class="cta-band__actions">
            <a class="btn btn--light" href="{{ lroute('contact') }}">{{ __('site.actions.contact_us') }} <x-icon name="arrow-right" class="icon--dir" /></a>
            @if ($email = site('email'))
                <a class="btn btn--outline-light" href="mailto:{{ $email }}"><x-icon name="mail" /> <bdi dir="ltr">{{ $email }}</bdi></a>
            @endif
        </div>
    </div>
</section>
