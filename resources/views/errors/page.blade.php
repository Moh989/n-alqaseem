{{-- Error page inside the full site layout (404, 419, 429, 413). --}}
@extends('layouts.site')

@section('body_class', 'page-error')

@section('content')
    <section class="error-page">
        <div class="container error-page__inner">
            <x-brand-mark class="error-page__mark" />
            <p class="error-page__code" aria-hidden="true">{{ $code }}</p>
            <h1 class="error-page__title">{{ __("site.errors.{$code}_title") }}</h1>
            <p class="error-page__text">{{ __("site.errors.{$code}_text") }}</p>
            <div class="error-page__actions">
                <a class="btn btn--primary" href="{{ lroute('home') }}">{{ __('site.actions.back_home') }}</a>
                <a class="btn btn--outline" href="{{ lroute('contact') }}">{{ __('site.actions.contact_us') }}</a>
            </div>
        </div>
    </section>
@endsection
