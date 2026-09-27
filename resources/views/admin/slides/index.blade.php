@extends('layouts.admin')

@section('title', __('admin.slides.title'))

@section('content')
    <div class="a-head">
        <h1 class="a-title">{{ __('admin.slides.title') }}</h1>
        <a class="btn btn--primary btn--sm" href="{{ route('admin.slides.create', ['page' => $page]) }}"><x-icon name="plus" /> {{ __('admin.slides.create') }}</a>
    </div>

    <nav class="tabs" aria-label="{{ __('admin.slides.page') }}">
        @foreach (\App\Models\Slide::PAGES as $key => $label)
            <a href="{{ route('admin.slides.index', ['page' => $key]) }}" @if ($key === $page) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
    </nav>

    <div class="table-wrap">
        <table class="a-table">
            <thead>
                <tr>
                    <th scope="col">{{ __('admin.common.image') }}</th>
                    <th scope="col">{{ __('admin.slides.heading') }}</th>
                    <th scope="col">{{ __('admin.common.published') }}</th>
                    <th scope="col">{{ __('admin.common.order') }}</th>
                    <th scope="col"><span class="visually-hidden">{{ __('admin.common.actions') }}</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($slides as $slide)
                    <tr>
                        <td class="a-table__thumb">
                            @if ($slide->media)
                                <img src="{{ $slide->media->url(480) }}" alt="" width="120" height="{{ $slide->media->heightFor(120) }}">
                            @else
                                <x-admin.pending-badge />
                            @endif
                        </td>
                        <td>
                            <strong>{{ $slide->heading_ar }}</strong>
                            <div class="muted" dir="ltr" lang="en">{{ $slide->heading_en }}</div>
                        </td>
                        <td>{{ $slide->is_published ? __('admin.common.yes') : __('admin.common.no') }}</td>
                        <td><x-admin.move route="admin.slides.move" :params="['slide' => $slide]" /></td>
                        <td class="a-table__actions">
                            <a class="btn btn--outline btn--sm" href="{{ route('admin.slides.edit', $slide) }}"><x-icon name="pencil" /> {{ __('admin.common.edit') }}</a>
                            <x-admin.delete :action="route('admin.slides.destroy', $slide)" />
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="muted">—</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
