@extends('layouts.site')

@section('body_class', 'page-contact')

@section('content')
    <x-page-hero
        :title="$intro?->tr('title') ?? __('site.nav.contact')"
        :eyebrow="__('site.contact.eyebrow')"
        :lead="$intro?->tr('body')"
        :crumbs="[['label' => __('site.nav.contact')]]"
    />

    <div class="section">
        <div class="container contact-layout">
            <aside class="contact-layout__aside" aria-labelledby="contact-details">
                <div class="aside-card">
                    <h2 class="aside-card__title" id="contact-details">{{ __('site.contact.details') }}</h2>
                    <dl class="contact-details">
                        @if ($address = site('address'))
                            <div>
                                <dt><x-icon name="map-pin" /> {{ __('site.contact.address') }}</dt>
                                <dd>
                                    <address>{{ $address }}</address>
                                    @if ($mapsUrl = site()->mapsUrl())
                                        <a class="link-arrow" href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer">
                                            {{ __('site.actions.open_map') }} <x-icon name="external-link" />
                                        </a>
                                    @endif
                                </dd>
                            </div>
                        @endif
                        @if ($email = site('email'))
                            <div>
                                <dt><x-icon name="mail" /> {{ __('site.contact.email') }}</dt>
                                <dd><a href="mailto:{{ $email }}"><bdi dir="ltr">{{ $email }}</bdi></a></dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </aside>

            <section class="contact-layout__main" aria-labelledby="contact-form-title">
                <div class="form-card" id="contact-form">
                    <h2 class="form-card__title" id="contact-form-title">{{ __('site.contact.form_title') }}</h2>

                    @if (session('contact_success'))
                        <div class="alert alert--success" role="status" tabindex="-1" data-focus-on-load>
                            <x-icon name="check" /> <p>{{ session('contact_success') }}</p>
                        </div>
                    @endif

                    @if (session('contact_error'))
                        <div class="alert alert--error" role="alert" tabindex="-1" data-focus-on-load>
                            <x-icon name="circle-alert" /> <p>{{ session('contact_error') }}</p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert--error" role="alert" tabindex="-1" data-focus-on-load>
                            <x-icon name="circle-alert" />
                            <div>
                                <p>{{ __('contact.error_summary') }}</p>
                                <ul>
                                    @foreach ($errors->keys() as $field)
                                        <li><a href="#field-{{ $field }}">{{ $errors->first($field) }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form class="form" method="POST" action="{{ lroute('contact.store') }}" novalidate data-contact-form>
                        @csrf
                        <input type="hidden" name="form_token" value="{{ $formToken }}">
                        <div class="form__hp" aria-hidden="true">
                            <label for="field-website">Website</label>
                            <input type="text" id="field-website" name="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="form__grid">
                            @foreach ([
                                ['name', 'text', 'name', true],
                                ['email', 'email', 'email', true],
                                ['organization', 'text', 'organization', false],
                            ] as [$field, $type, $autocomplete, $required])
                                <div class="field @error($field) field--invalid @enderror">
                                    <label class="field__label" for="field-{{ $field }}">
                                        {{ __('contact.fields.'.$field) }}
                                        @if ($required)
                                            <span class="field__req" aria-hidden="true">*</span>
                                        @else
                                            <span class="field__opt">({{ __('contact.optional') }})</span>
                                        @endif
                                    </label>
                                    <input class="field__input" id="field-{{ $field }}" name="{{ $field }}" type="{{ $type }}"
                                           value="{{ old($field) }}" autocomplete="{{ $autocomplete }}"
                                           @if ($required) required aria-required="true" @endif
                                           @if ($type === 'email') dir="ltr" @endif
                                           maxlength="{{ $field === 'email' ? 191 : 160 }}"
                                           @error($field) aria-invalid="true" aria-describedby="error-{{ $field }}" @enderror>
                                    @error($field)
                                        <p class="field__error" id="error-{{ $field }}">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach

                            <div class="field @error('inquiry_type') field--invalid @enderror">
                                <label class="field__label" for="field-inquiry_type">{{ __('contact.fields.inquiry_type') }} <span class="field__req" aria-hidden="true">*</span></label>
                                <select class="field__input" id="field-inquiry_type" name="inquiry_type" required aria-required="true"
                                        @error('inquiry_type') aria-invalid="true" aria-describedby="error-inquiry_type" @enderror>
                                    <option value="">{{ __('contact.choose') }}</option>
                                    @foreach ($inquiryTypes as $type)
                                        <option value="{{ $type }}" @selected(old('inquiry_type', $selectedType) === $type)>{{ __('contact.types.'.$type) }}</option>
                                    @endforeach
                                </select>
                                @error('inquiry_type')
                                    <p class="field__error" id="error-inquiry_type">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="field field--full @error('message') field--invalid @enderror">
                                <label class="field__label" for="field-message">{{ __('contact.fields.message') }} <span class="field__req" aria-hidden="true">*</span></label>
                                <textarea class="field__input field__textarea" id="field-message" name="message" rows="6" required aria-required="true"
                                          minlength="10" maxlength="5000" placeholder="{{ __('contact.placeholders.message') }}"
                                          aria-describedby="message-count @error('message') error-message @enderror"
                                          @error('message') aria-invalid="true" @enderror data-char-count>{{ old('message') }}</textarea>
                                <p class="field__hint" id="message-count" aria-live="polite"><span data-char-count-value>{{ mb_strlen((string) old('message')) }}</span> / 5000 {{ __('contact.characters') }}</p>
                                @error('message')
                                    <p class="field__error" id="error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="field field--full field--check @error('consent') field--invalid @enderror">
                                <input type="checkbox" id="field-consent" name="consent" value="1" @checked(old('consent')) required aria-required="true"
                                       @error('consent') aria-invalid="true" aria-describedby="error-consent" @enderror>
                                <label for="field-consent">
                                    {!! __('contact.consent_label', ['link' => '<a href="'.e(lroute('privacy')).'">'.e(__('contact.consent_link')).'</a>']) !!}
                                </label>
                                @error('consent')
                                    <p class="field__error" id="error-consent">{{ $message }}</p>
                                @enderror
                            </div>

                            @if ($turnstileKey)
                                <div class="field field--full">
                                    <div class="cf-turnstile" data-sitekey="{{ $turnstileKey }}" data-language="{{ $locale }}"></div>
                                    @error('cf-turnstile-response')
                                        <p class="field__error">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <button class="btn btn--primary btn--lg" type="submit" data-submit data-sending-label="{{ __('contact.sending') }}">
                            {{ __('contact.submit') }} <x-icon name="arrow-right" class="icon--dir" />
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>

    @if ($turnstileKey)
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
@endsection
