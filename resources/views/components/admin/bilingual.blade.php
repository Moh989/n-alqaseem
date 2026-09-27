@props([
    'name',
    'label',
    'model' => null,
    'type' => 'text',
    'rows' => 4,
    'requiredAr' => false,
    'requiredEn' => false,
    'maxlength' => null,
    'help' => null,
    'pending' => false,
])
@php
    $fields = ['ar' => ['label' => __('admin.common.arabic'), 'dir' => 'rtl', 'required' => $requiredAr], 'en' => ['label' => __('admin.common.english'), 'dir' => 'ltr', 'required' => $requiredEn]];
@endphp
<fieldset {{ $attributes->class(['bi-field']) }}>
    <legend class="bi-field__legend">
        {{ $label }}
        @if ($pending)
            <x-admin.pending-badge />
        @endif
    </legend>
    <div class="bi-field__grid">
        @foreach ($fields as $lang => $meta)
            @php($field = $name.'_'.$lang)
            @php($value = old($field, $model?->getAttribute($field)))
            <div class="a-field @error($field) a-field--invalid @enderror">
                <label class="a-field__label" for="f-{{ $field }}">
                    {{ $meta['label'] }}
                    @if ($meta['required']) <span class="a-field__req" aria-hidden="true">*</span> @endif
                </label>
                @if ($type === 'textarea')
                    <textarea class="a-input" id="f-{{ $field }}" name="{{ $field }}" rows="{{ $rows }}" dir="{{ $meta['dir'] }}" lang="{{ $lang }}"
                              @if ($maxlength) maxlength="{{ $maxlength }}" data-counter @endif
                              @if ($meta['required']) required @endif>{{ $value }}</textarea>
                @else
                    <input class="a-input" id="f-{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ $value }}" dir="{{ $meta['dir'] }}" lang="{{ $lang }}"
                           @if ($maxlength) maxlength="{{ $maxlength }}" data-counter @endif
                           @if ($meta['required']) required @endif>
                @endif
                @error($field)
                    <p class="a-field__error">{{ $message }}</p>
                @enderror
            </div>
        @endforeach
    </div>
    @if ($help)
        <p class="a-field__help">{{ $help }}</p>
    @endif
</fieldset>
