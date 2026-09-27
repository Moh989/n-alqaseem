@extends('layouts.admin')

@section('title', __('admin.categories.title'))

@section('content')
    <div class="a-head">
        <h1 class="a-title">{{ __('admin.categories.title') }}</h1>
        <a class="btn btn--primary btn--sm" href="{{ route('admin.categories.create') }}"><x-icon name="plus" /> {{ __('admin.categories.create') }}</a>
    </div>
    <div class="table-wrap">
        <table class="a-table">
            <thead>
                <tr>
                    <th scope="col">{{ __('admin.categories.name') }}</th>
                    <th scope="col">{{ __('admin.categories.services_count') }}</th>
                    <th scope="col">{{ __('admin.common.order') }}</th>
                    <th scope="col"><span class="visually-hidden">{{ __('admin.common.actions') }}</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td><strong>{{ $category->name_ar }}</strong> <span class="muted" lang="en" dir="ltr">{{ $category->name_en }}</span></td>
                        <td>{{ $category->services_count }}</td>
                        <td>{{ $category->sort }}</td>
                        <td class="a-table__actions">
                            <a class="btn btn--outline btn--sm" href="{{ route('admin.categories.edit', $category) }}"><x-icon name="pencil" /> {{ __('admin.common.edit') }}</a>
                            @if ($category->services_count === 0)
                                <x-admin.delete :action="route('admin.categories.destroy', $category)" />
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
