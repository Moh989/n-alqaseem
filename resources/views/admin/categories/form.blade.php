@extends('layouts.admin')

@section('title', $category->exists ? __('admin.categories.edit') : __('admin.categories.create'))

@section('content')
    <div class="a-head">
        <h1 class="a-title">{{ $category->exists ? __('admin.categories.edit') : __('admin.categories.create') }}</h1>
        <a class="btn btn--ghost btn--sm" href="{{ route('admin.categories.index') }}">{{ __('admin.common.back') }}</a>
    </div>
    <form class="panel stack" method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf
        @if ($category->exists) @method('PUT') @endif
        <div class="bi-field__grid">
            <div class="a-field">
                <label class="a-field__label" for="f-slug">{{ __('admin.services.slug') }} <span class="a-field__req" aria-hidden="true">*</span></label>
                <input class="a-input" id="f-slug" name="slug" value="{{ old('slug', $category->slug) }}" required dir="ltr" pattern="[a-z0-9]+(-[a-z0-9]+)*">
                <p class="a-field__help">{{ __('admin.services.slug_help') }}</p>
            </div>
            <div class="a-field">
                <label class="a-field__label" for="f-sort">{{ __('admin.common.order') }}</label>
                <input class="a-input" id="f-sort" name="sort" type="number" min="0" max="999" value="{{ old('sort', $category->sort ?? 0) }}" dir="ltr">
            </div>
        </div>
        <x-admin.bilingual name="name" :label="__('admin.categories.name')" :model="$category" :required-ar="true" :required-en="true" maxlength="255" />
        <x-admin.bilingual name="description" :label="__('admin.categories.description')" :model="$category" type="textarea" :rows="3" maxlength="1000" />
        <div class="form-actions">
            <button class="btn btn--primary" type="submit"><x-icon name="save" /> {{ __('admin.common.save') }}</button>
        </div>
    </form>
@endsection
