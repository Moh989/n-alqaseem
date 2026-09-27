@extends('layouts.admin')

@section('title', __('admin.seo.title'))

@section('content')
    <h1 class="a-title">{{ __('admin.seo.title') }}</h1>
    <div class="table-wrap">
        <table class="a-table">
            <thead>
                <tr>
                    <th scope="col">{{ __('admin.seo.page') }}</th>
                    <th scope="col">{{ __('admin.seo.meta_title') }}</th>
                    <th scope="col">{{ __('admin.seo.og_image') }}</th>
                    <th scope="col"><span class="visually-hidden">{{ __('admin.common.actions') }}</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pages as $key => $label)
                    @php($meta = $metas->get($key))
                    <tr>
                        <td><strong>{{ $label }}</strong></td>
                        <td>
                            {{ $meta?->title_ar ?? '—' }}
                            @if (! $meta || ! $meta->hasBothLanguages('description')) <x-admin.pending-badge /> @endif
                        </td>
                        <td>{{ $meta?->ogMedia ? __('admin.common.yes') : __('admin.common.no') }}</td>
                        <td class="a-table__actions"><a class="btn btn--outline btn--sm" href="{{ route('admin.seo.edit', $key) }}"><x-icon name="pencil" /> {{ __('admin.common.edit') }}</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
