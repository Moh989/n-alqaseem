@extends('layouts.admin')

@section('title', __('admin.services.title'))

@section('content')
    <div class="a-head">
        <h1 class="a-title">{{ __('admin.services.title') }}</h1>
        <a class="btn btn--primary btn--sm" href="{{ route('admin.services.create') }}"><x-icon name="plus" /> {{ __('admin.services.create') }}</a>
    </div>

    @foreach ($categories as $category)
        <section class="panel" aria-labelledby="cat-{{ $category->id }}">
            <h2 class="panel__title" id="cat-{{ $category->id }}">{{ $category->name_ar }}</h2>
            <div class="table-wrap">
                <table class="a-table">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('admin.common.image') }}</th>
                            <th scope="col">{{ __('admin.services.name') }}</th>
                            <th scope="col">{{ __('admin.common.published') }}</th>
                            <th scope="col">{{ __('admin.common.order') }}</th>
                            <th scope="col"><span class="visually-hidden">{{ __('admin.common.actions') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($category->services as $service)
                            <tr>
                                <td class="a-table__thumb">
                                    @if ($service->media)
                                        <img src="{{ $service->media->url(480) }}" alt="" width="120" height="{{ $service->media->heightFor(120) }}">
                                    @else
                                        <x-admin.pending-badge />
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $service->title_ar }}</strong>
                                    <div class="muted" dir="ltr" lang="en">{{ $service->title_en }} · /services/{{ $service->slug }}</div>
                                </td>
                                <td>{{ $service->is_published ? __('admin.common.yes') : __('admin.common.no') }}</td>
                                <td><x-admin.move route="admin.services.move" :params="['service' => $service]" /></td>
                                <td class="a-table__actions">
                                    @if ($service->is_published)
                                        <a class="btn btn--ghost btn--sm" href="{{ $service->url('ar') }}" target="_blank" rel="noopener"><x-icon name="eye" /> {{ __('admin.common.view') }}</a>
                                    @endif
                                    <a class="btn btn--outline btn--sm" href="{{ route('admin.services.edit', $service) }}"><x-icon name="pencil" /> {{ __('admin.common.edit') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="muted">—</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endforeach
@endsection
