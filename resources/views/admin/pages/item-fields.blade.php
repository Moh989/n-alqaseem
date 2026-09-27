@php($prefix = $item ? 'i'.$item->id : 'new')
<div class="bi-field__grid">
    <div class="a-field">
        <label class="a-field__label" for="{{ $prefix }}-title_ar">{{ __('admin.pages.item_title') }} ({{ __('admin.common.arabic') }}) <span class="a-field__req" aria-hidden="true">*</span></label>
        <input class="a-input" id="{{ $prefix }}-title_ar" name="title_ar" value="{{ $item?->title_ar }}" required maxlength="255" dir="rtl">
    </div>
    <div class="a-field">
        <label class="a-field__label" for="{{ $prefix }}-title_en">{{ __('admin.pages.item_title') }} ({{ __('admin.common.english') }})</label>
        <input class="a-input" id="{{ $prefix }}-title_en" name="title_en" value="{{ $item?->title_en }}" maxlength="255" dir="ltr" lang="en">
    </div>
    <div class="a-field">
        <label class="a-field__label" for="{{ $prefix }}-text_ar">{{ __('admin.pages.item_text') }} ({{ __('admin.common.arabic') }})</label>
        <textarea class="a-input" id="{{ $prefix }}-text_ar" name="text_ar" rows="3" maxlength="2000" dir="rtl">{{ $item?->text_ar }}</textarea>
    </div>
    <div class="a-field">
        <label class="a-field__label" for="{{ $prefix }}-text_en">{{ __('admin.pages.item_text') }} ({{ __('admin.common.english') }})</label>
        <textarea class="a-input" id="{{ $prefix }}-text_en" name="text_en" rows="3" maxlength="2000" dir="ltr" lang="en">{{ $item?->text_en }}</textarea>
    </div>
</div>
<div class="a-field a-field--narrow">
    <label class="a-field__label" for="{{ $prefix }}-icon">الأيقونة</label>
    <select class="a-input" id="{{ $prefix }}-icon" name="icon">
        <option value="">—</option>
        @foreach (\App\Http\Controllers\Admin\SectionItemController::ICONS as $icon)
            <option value="{{ $icon }}" @selected($item?->icon === $icon)>{{ $icon }}</option>
        @endforeach
    </select>
</div>
