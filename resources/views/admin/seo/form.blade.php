@extends('layouts.admin')

@section('title', __('admin.seo.title').': '.\App\Models\SeoMeta::PAGES[$page])

@section('content')
    <div class="a-head">
        <h1 class="a-title">{{ __('admin.seo.title') }}: {{ \App\Models\SeoMeta::PAGES[$page] }}</h1>
        <a class="btn btn--ghost btn--sm" href="{{ route('admin.seo.index') }}">{{ __('admin.common.back') }}</a>
    </div>
    <form class="panel stack" method="POST" enctype="multipart/form-data" action="{{ route('admin.seo.update', $page) }}">
        @csrf
        @method('PUT')
        <x-admin.bilingual name="title" :label="__('admin.seo.meta_title')" :model="$meta" maxlength="255" :help="__('admin.seo.title_hint')" />
        <x-admin.bilingual name="description" :label="__('admin.seo.meta_description')" :model="$meta" type="textarea" :rows="3" maxlength="320" :help="__('admin.seo.description_hint')" />
        <x-admin.image-field name="og_image" :media="$meta->ogMedia" :label="__('admin.seo.og_image')" :min-width="600" />
        <div class="form-actions">
            <button class="btn btn--primary" type="submit"><x-icon name="save" /> {{ __('admin.common.save') }}</button>
        </div>
    </form>
@endsection
