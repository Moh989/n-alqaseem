@extends('layouts.admin')

@section('title', __('admin.settings.title'))

@section('content')
    <h1 class="a-title">{{ __('admin.settings.title') }}</h1>
    <p class="a-lead">{{ __('admin.settings.help') }}</p>

    @foreach ($groups as $group => $settings)
        <form class="panel" method="POST" action="{{ route('admin.settings.update', $group) }}" id="group-{{ $group }}">
            @csrf
            @method('PUT')
            <h2 class="panel__title">{{ __('admin.settings.groups.'.$group) }}</h2>

            @foreach ($settings as $setting)
                @php($base = 'settings.'.$setting->key)
                <div class="setting-row" id="setting-{{ $setting->key }}">
                    <div class="setting-row__head">
                        <h3 class="setting-row__label">{{ $setting->label() }}</h3>
                        @if (! $setting->isApproved() || ($setting->type !== 'boolean' && ! $setting->isFilled()))
                            <x-admin.pending-badge />
                        @else
                            <span class="approved-badge">{{ __('admin.common.approved') }}</span>
                        @endif
                    </div>

                    @if ($setting->type === 'boolean')
                        {{-- Confirmation-only setting --}}
                    @elseif ($setting->is_translatable)
                        <div class="bi-field__grid">
                            @foreach (['ar' => 'rtl', 'en' => 'ltr'] as $lang => $dir)
                                <div class="a-field @error($base.'.value_'.$lang) a-field--invalid @enderror">
                                    <label class="a-field__label" for="s-{{ $setting->key }}-{{ $lang }}">{{ __('admin.common.'.($lang === 'ar' ? 'arabic' : 'english')) }}</label>
                                    <input class="a-input" id="s-{{ $setting->key }}-{{ $lang }}" name="settings[{{ $setting->key }}][value_{{ $lang }}]" dir="{{ $dir }}" lang="{{ $lang }}"
                                           value="{{ old($base.'.value_'.$lang, $setting->getAttribute('value_'.$lang)) }}">
                                    @error($base.'.value_'.$lang) <p class="a-field__error">{{ $message }}</p> @enderror
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="a-field @error($base.'.value') a-field--invalid @enderror">
                            <label class="a-field__label visually-hidden" for="s-{{ $setting->key }}">{{ $setting->label() }}</label>
                            @if ($setting->type === 'textarea')
                                <textarea class="a-input" id="s-{{ $setting->key }}" name="settings[{{ $setting->key }}][value]" rows="3" dir="{{ str_ends_with($setting->key, '_en') ? 'ltr' : 'auto' }}">{{ old($base.'.value', $setting->value) }}</textarea>
                            @else
                                <input class="a-input" id="s-{{ $setting->key }}" name="settings[{{ $setting->key }}][value]"
                                       type="{{ match ($setting->type) { 'email' => 'email', 'url' => 'url', 'year' => 'number', default => 'text' } }}"
                                       @if (in_array($setting->type, ['email', 'url', 'year'], true)) dir="ltr" @endif
                                       value="{{ old($base.'.value', $setting->value) }}">
                            @endif
                            @error($base.'.value') <p class="a-field__error">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <label class="checkbox">
                        <input type="checkbox" name="settings[{{ $setting->key }}][approved]" value="1" @checked(old($base.'.approved', $setting->isApproved()))>
                        <span>{{ __('admin.common.approve') }}</span>
                    </label>

                    @if ($setting->admin_note)
                        <p class="a-field__help"><x-icon name="circle-alert" /> {{ $setting->admin_note }}</p>
                    @endif
                </div>
            @endforeach

            <div class="form-actions">
                <button class="btn btn--primary" type="submit"><x-icon name="save" /> {{ __('admin.common.save_changes') }}</button>
            </div>
        </form>
    @endforeach
@endsection
