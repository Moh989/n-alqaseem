@extends('layouts.admin')

@section('title', $service->exists ? __('admin.services.edit') : __('admin.services.create'))

@section('content')
    <div class="a-head">
        <h1 class="a-title">{{ $service->exists ? __('admin.services.edit').': '.$service->title_ar : __('admin.services.create') }}</h1>
        <a class="btn btn--ghost btn--sm" href="{{ route('admin.services.index') }}">{{ __('admin.common.back') }}</a>
    </div>

    <p class="notice notice--info"><x-icon name="circle-alert" /> {{ __('admin.services.scope_note') }}</p>

    <form class="panel stack" method="POST" enctype="multipart/form-data"
          action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}">
        @csrf
        @if ($service->exists) @method('PUT') @endif

        <div class="bi-field__grid bi-field__grid--3">
            <div class="a-field">
                <label class="a-field__label" for="f-category">{{ __('admin.services.category') }} <span class="a-field__req" aria-hidden="true">*</span></label>
                <select class="a-input" id="f-category" name="service_category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((int) old('service_category_id', $service->service_category_id) === $category->id)>{{ $category->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div class="a-field">
                <label class="a-field__label" for="f-slug">{{ __('admin.services.slug') }} <span class="a-field__req" aria-hidden="true">*</span></label>
                <input class="a-input" id="f-slug" name="slug" value="{{ old('slug', $service->slug) }}" required dir="ltr" pattern="[a-z0-9]+(-[a-z0-9]+)*" maxlength="120">
                <p class="a-field__help">{{ __('admin.services.slug_help') }}</p>
            </div>
            <div class="a-field">
                <label class="a-field__label" for="f-icon">الأيقونة</label>
                <select class="a-input" id="f-icon" name="icon">
                    <option value="">—</option>
                    @foreach (\App\Http\Controllers\Admin\ServiceController::ICONS as $icon)
                        <option value="{{ $icon }}" @selected(old('icon', $service->icon) === $icon)>{{ $icon }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <x-admin.bilingual name="title" :label="__('admin.services.name')" :model="$service" :required-ar="true" maxlength="255" :pending="$service->exists && blank($service->title_en)" />
        <x-admin.bilingual name="summary" :label="__('admin.services.summary')" :model="$service" type="textarea" :rows="3" maxlength="500" />
        <x-admin.bilingual name="body" :label="__('admin.services.body')" :model="$service" type="textarea" :rows="12" :help="__('admin.common.formatting_help')" />
        <x-admin.image-field :media="$service->media" />

        <details class="details-panel">
            <summary>{{ __('admin.services.seo') }}</summary>
            <x-admin.bilingual name="seo_title" :label="__('admin.services.seo_title')" :model="$service" maxlength="255" :help="__('admin.seo.title_hint')" />
            <x-admin.bilingual name="seo_description" :label="__('admin.services.seo_description')" :model="$service" type="textarea" :rows="2" maxlength="320" :help="__('admin.seo.description_hint')" />
        </details>

        <div class="form-actions">
            <x-admin.publish :checked="$service->is_published" />
            <button class="btn btn--primary" type="submit"><x-icon name="save" /> {{ __('admin.common.save') }}</button>
        </div>
    </form>

    @if ($service->exists)
        <div class="danger-zone">
            <x-admin.delete :action="route('admin.services.destroy', $service)" />
        </div>
    @endif
@endsection
