@extends('layouts.admin')

@section('title', $slide->exists ? __('admin.slides.edit') : __('admin.slides.create'))

@section('content')
    <div class="a-head">
        <h1 class="a-title">{{ $slide->exists ? __('admin.slides.edit') : __('admin.slides.create') }}</h1>
        <a class="btn btn--ghost btn--sm" href="{{ route('admin.slides.index', ['page' => $slide->page]) }}">{{ __('admin.common.back') }}</a>
    </div>

    <form class="panel stack" method="POST" enctype="multipart/form-data"
          action="{{ $slide->exists ? route('admin.slides.update', $slide) : route('admin.slides.store') }}">
        @csrf
        @if ($slide->exists) @method('PUT') @endif

        <div class="a-field a-field--narrow">
            <label class="a-field__label" for="f-page">{{ __('admin.slides.page') }}</label>
            <select class="a-input" id="f-page" name="page" required>
                @foreach (\App\Models\Slide::PAGES as $key => $label)
                    <option value="{{ $key }}" @selected(old('page', $slide->page) === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <x-admin.image-field :media="$slide->media" :required="! $slide->exists" :min-width="1280" />
        <x-admin.bilingual name="heading" :label="__('admin.slides.heading')" :model="$slide" :required-ar="true" maxlength="160" />
        <x-admin.bilingual name="text" :label="__('admin.slides.text')" :model="$slide" type="textarea" :rows="3" maxlength="400" />

        @foreach ([1, 2] as $n)
            <fieldset class="bi-field">
                <legend class="bi-field__legend">{{ __('admin.slides.cta'.$n) }}</legend>
                <div class="bi-field__grid bi-field__grid--3">
                    <div class="a-field">
                        <label class="a-field__label" for="f-cta{{ $n }}_label_ar">{{ __('admin.slides.label') }} ({{ __('admin.common.arabic') }})</label>
                        <input class="a-input" id="f-cta{{ $n }}_label_ar" name="cta{{ $n }}_label_ar" maxlength="60" value="{{ old('cta'.$n.'_label_ar', $slide->getAttribute('cta'.$n.'_label_ar')) }}">
                    </div>
                    <div class="a-field">
                        <label class="a-field__label" for="f-cta{{ $n }}_label_en">{{ __('admin.slides.label') }} ({{ __('admin.common.english') }})</label>
                        <input class="a-input" id="f-cta{{ $n }}_label_en" name="cta{{ $n }}_label_en" maxlength="60" dir="ltr" lang="en" value="{{ old('cta'.$n.'_label_en', $slide->getAttribute('cta'.$n.'_label_en')) }}">
                    </div>
                    <div class="a-field">
                        <label class="a-field__label" for="f-cta{{ $n }}_target">{{ __('admin.slides.target') }}</label>
                        <select class="a-input" id="f-cta{{ $n }}_target" name="cta{{ $n }}_target">
                            @foreach (__('admin.slides.targets') as $value => $label)
                                <option value="{{ $value }}" @selected(old('cta'.$n.'_target', $slide->getAttribute('cta'.$n.'_target')) === $value || ($value === '' && ! $slide->getAttribute('cta'.$n.'_target')))>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </fieldset>
        @endforeach

        <div class="form-actions">
            <x-admin.publish :checked="$slide->is_published" />
            <button class="btn btn--primary" type="submit"><x-icon name="save" /> {{ __('admin.common.save') }}</button>
        </div>
    </form>
@endsection
