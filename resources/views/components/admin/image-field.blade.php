@props(['media' => null, 'name' => 'image', 'label' => null, 'required' => false, 'minWidth' => null])
<fieldset {{ $attributes->class(['bi-field']) }}>
    <legend class="bi-field__legend">
        {{ $label ?? __('admin.common.image') }}
        @if (! $media)
            <x-admin.pending-badge />
        @endif
    </legend>
    <div class="image-field">
        <div class="image-field__preview" data-image-preview>
            @if ($media)
                <img src="{{ $media->url(480) }}" alt="{{ $media->alt_ar }}" width="240" height="{{ $media->heightFor(240) }}">
                @if ($media->is_stock)
                    <span class="tag">{{ __('admin.common.stock') }}</span>
                @endif
            @else
                <span class="image-field__empty"><x-icon name="image" /></span>
            @endif
        </div>
        <div class="image-field__inputs">
            <div class="a-field @error($name) a-field--invalid @enderror">
                <label class="a-field__label" for="f-{{ $name }}">{{ $media ? __('admin.common.replace_image') : __('admin.common.image') }} @if ($required) <span class="a-field__req" aria-hidden="true">*</span> @endif</label>
                <input class="a-input" id="f-{{ $name }}" name="{{ $name }}" type="file" accept="image/jpeg,image/png,image/webp" @if ($required) required @endif data-image-input>
                <p class="a-field__help">{{ __('admin.upload.image_help') }} @if ($minWidth) {{ __('admin.upload.dimensions_small', ['min' => $minWidth]) }} @endif</p>
                @error($name)
                    <p class="a-field__error">{{ $message }}</p>
                @enderror
            </div>
            <div class="bi-field__grid">
                <div class="a-field">
                    <label class="a-field__label" for="f-alt_ar">{{ __('admin.common.alt_ar') }}</label>
                    <input class="a-input" id="f-alt_ar" name="alt_ar" type="text" maxlength="255" dir="rtl" value="{{ old('alt_ar', $media?->alt_ar) }}">
                </div>
                <div class="a-field">
                    <label class="a-field__label" for="f-alt_en">{{ __('admin.common.alt_en') }}</label>
                    <input class="a-input" id="f-alt_en" name="alt_en" type="text" maxlength="255" dir="ltr" lang="en" value="{{ old('alt_en', $media?->alt_en) }}">
                </div>
            </div>
        </div>
    </div>
</fieldset>
