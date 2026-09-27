@extends('layouts.admin')

@section('title', $pageInfo['ar'])

@section('content')
    <div class="a-head">
        <h1 class="a-title">{{ __('admin.pages.title') }}: {{ $pageInfo['ar'] }}</h1>
        <a class="btn btn--ghost btn--sm" href="{{ route('admin.pages.index') }}">{{ __('admin.common.back') }}</a>
    </div>
    <p class="a-lead">{{ __('admin.common.formatting_help') }}</p>

    @foreach ($sections as $section)
        <section class="panel" id="section-{{ $section->id }}" aria-labelledby="section-title-{{ $section->id }}">
            <div class="panel__head">
                <h2 class="panel__title" id="section-title-{{ $section->id }}">{{ $section->title_ar ?: $section->key }} <small class="muted" dir="ltr">{{ $section->key }}</small></h2>
                <div class="panel__tools">
                    <x-admin.move route="admin.sections.move" :params="['section' => $section]" />
                    @unless ($pageInfo['fixed'])
                        <x-admin.delete :action="route('admin.sections.destroy', $section)" />
                    @endunless
                </div>
            </div>

            <form method="POST" action="{{ route('admin.sections.update', $section) }}" class="stack">
                @csrf
                @method('PUT')
                <x-admin.bilingual name="title" :label="__('admin.pages.section_title')" :model="$section" maxlength="255" />
                <x-admin.bilingual name="body" :label="__('admin.pages.section_body')" :model="$section" type="textarea" :rows="6" />
                <div class="form-actions">
                    <x-admin.publish :checked="$section->is_published" :label="__('admin.pages.visible')" />
                    <button class="btn btn--primary" type="submit"><x-icon name="save" /> {{ __('admin.common.save') }}</button>
                </div>
            </form>

            @if ($section->items->isNotEmpty() || in_array($section->key, ['values', 'scope', 'stages', 'process', 'structure'], true))
                <div class="items">
                    <h3 class="items__title">{{ __('admin.pages.items') }}</h3>
                    @foreach ($section->items as $item)
                        <details class="item">
                            <summary>
                                <x-icon :name="$item->icon ?? 'list'" />
                                <span>{{ $item->title_ar }}</span>
                                @if (blank($item->title_en)) <x-admin.pending-badge /> @endif
                            </summary>
                            <form method="POST" action="{{ route('admin.items.update', $item) }}" class="stack">
                                @csrf
                                @method('PUT')
                                @include('admin.pages.item-fields', ['item' => $item])
                                <div class="form-actions">
                                    <button class="btn btn--primary btn--sm" type="submit"><x-icon name="save" /> {{ __('admin.common.save') }}</button>
                                </div>
                            </form>
                            <div class="item__tools">
                                <x-admin.move route="admin.items.move" :params="['item' => $item]" />
                                <x-admin.delete :action="route('admin.items.destroy', $item)" />
                            </div>
                        </details>
                    @endforeach

                    <details class="item item--new">
                        <summary><x-icon name="plus" /> {{ __('admin.pages.add_item') }}</summary>
                        <form method="POST" action="{{ route('admin.items.store', $section) }}" class="stack">
                            @csrf
                            @include('admin.pages.item-fields', ['item' => null])
                            <div class="form-actions">
                                <button class="btn btn--primary btn--sm" type="submit"><x-icon name="plus" /> {{ __('admin.common.create') }}</button>
                            </div>
                        </form>
                    </details>
                </div>
            @endif
        </section>
    @endforeach

    @unless ($pageInfo['fixed'])
        <section class="panel" aria-labelledby="new-section">
            <h2 class="panel__title" id="new-section">{{ __('admin.pages.add_section') }}</h2>
            <form method="POST" action="{{ route('admin.sections.store', $page) }}" class="stack">
                @csrf
                <x-admin.bilingual name="title" :label="__('admin.pages.section_title')" maxlength="255" />
                <x-admin.bilingual name="body" :label="__('admin.pages.section_body')" type="textarea" :rows="5" />
                <div class="form-actions">
                    <x-admin.publish :checked="true" :label="__('admin.pages.visible')" />
                    <button class="btn btn--primary" type="submit"><x-icon name="plus" /> {{ __('admin.common.create') }}</button>
                </div>
            </form>
        </section>
    @endunless
@endsection
