@extends('layouts.admin')

@section('title', __('admin.profile_pdf.title'))

@section('content')
    <h1 class="a-title">{{ __('admin.profile_pdf.title') }}</h1>
    <p class="notice notice--info"><x-icon name="circle-alert" /> {{ __('admin.profile_pdf.help') }}</p>

    <div class="a-grid a-grid--2">
        @foreach ($files as $locale => $setting)
            <section class="panel stack" aria-labelledby="pdf-{{ $locale }}">
                <div class="panel__head">
                    <h2 class="panel__title" id="pdf-{{ $locale }}">{{ $locale === 'ar' ? __('admin.common.arabic') : __('admin.common.english') }}</h2>
                    @if ($setting->isApproved() && $setting->value)
                        <span class="approved-badge">{{ __('admin.common.approved') }}</span>
                    @else
                        <x-admin.pending-badge />
                    @endif
                </div>

                @if ($setting->value)
                    <p><x-icon name="file-text" /> {{ __('admin.profile_pdf.current') }}: <code dir="ltr">{{ basename($setting->value) }}</code></p>
                    <form method="POST" action="{{ route('admin.profile-pdf.update', $locale) }}" class="stack stack--tight">
                        @csrf
                        @method('PUT')
                        <label class="checkbox">
                            <input type="hidden" name="approved" value="0">
                            <input type="checkbox" name="approved" value="1" @checked($setting->isApproved())>
                            <span>{{ __('admin.profile_pdf.approve') }}</span>
                        </label>
                        <div class="form-actions">
                            <button class="btn btn--primary btn--sm" type="submit">{{ __('admin.common.save') }}</button>
                            @if ($setting->isApproved())
                                <a class="btn btn--ghost btn--sm" href="{{ lroute('profile.download', [], $locale) }}" target="_blank" rel="noopener"><x-icon name="eye" /> {{ __('admin.profile_pdf.preview') }}</a>
                            @endif
                        </div>
                    </form>
                    <x-admin.delete :action="route('admin.profile-pdf.destroy', $locale)" :label="__('admin.profile_pdf.remove')" />
                @else
                    <p class="muted">{{ __('admin.profile_pdf.none') }}</p>
                @endif

                <form method="POST" enctype="multipart/form-data" action="{{ route('admin.profile-pdf.store', $locale) }}" class="stack stack--tight upload-box">
                    @csrf
                    <label class="a-field__label" for="pdf-file-{{ $locale }}">{{ __('admin.profile_pdf.upload') }}</label>
                    <input class="a-input" id="pdf-file-{{ $locale }}" name="pdf" type="file" accept="application/pdf" required>
                    <button class="btn btn--outline btn--sm" type="submit"><x-icon name="download" /> {{ __('admin.profile_pdf.upload') }}</button>
                </form>
            </section>
        @endforeach
    </div>
@endsection
