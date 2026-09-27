@extends('layouts.admin')

@section('title', __('admin.messages.title'))

@section('content')
    <h1 class="a-title">{{ __('admin.messages.title') }}</h1>
    <nav class="tabs" aria-label="{{ __('admin.messages.title') }}">
        <a href="{{ route('admin.messages.index') }}" @unless ($archived) aria-current="page" @endunless>{{ __('admin.messages.inbox') }}</a>
        <a href="{{ route('admin.messages.index', ['box' => 'archived']) }}" @if ($archived) aria-current="page" @endif>{{ __('admin.messages.archived') }}</a>
    </nav>

    <div class="table-wrap">
        <table class="a-table">
            <thead>
                <tr>
                    <th scope="col">{{ __('admin.messages.from') }}</th>
                    <th scope="col">{{ __('admin.messages.type') }}</th>
                    <th scope="col">{{ __('admin.messages.date') }}</th>
                    <th scope="col"><span class="visually-hidden">{{ __('admin.common.actions') }}</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($messages as $message)
                    <tr class="{{ $message->read_at ? '' : 'is-unread' }}">
                        <td>
                            <strong>{{ $message->name }}</strong>
                            @unless ($message->read_at) <span class="tag tag--new">{{ __('admin.messages.new') }}</span> @endunless
                            <div class="muted">{{ $message->organization }} <span dir="ltr">{{ $message->email }}</span></div>
                        </td>
                        <td>{{ $message->inquiryLabel() }}</td>
                        <td><time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->format('Y-m-d H:i') }}</time></td>
                        <td class="a-table__actions"><a class="btn btn--outline btn--sm" href="{{ route('admin.messages.show', $message) }}"><x-icon name="eye" /> {{ __('admin.common.view') }}</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="muted">{{ __('admin.messages.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $messages->links('pagination::bootstrap-4') }}
@endsection
