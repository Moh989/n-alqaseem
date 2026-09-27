@extends('layouts.admin')

@section('title', __('admin.pages.title'))

@section('content')
    <h1 class="a-title">{{ __('admin.pages.title') }}</h1>
    <div class="table-wrap">
        <table class="a-table">
            <thead>
                <tr>
                    <th scope="col">{{ __('admin.slides.page') }}</th>
                    <th scope="col">{{ __('admin.pages.sections') }}</th>
                    <th scope="col"><span class="visually-hidden">{{ __('admin.common.actions') }}</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pages as $key => $info)
                    <tr>
                        <td><strong>{{ $info['ar'] }}</strong> <span class="muted" lang="en">{{ $info['en'] }}</span></td>
                        <td>{{ $counts[$key] ?? 0 }}</td>
                        <td class="a-table__actions"><a class="btn btn--outline btn--sm" href="{{ route('admin.pages.edit', $key) }}"><x-icon name="pencil" /> {{ __('admin.common.edit') }}</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
