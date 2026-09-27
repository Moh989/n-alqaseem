@extends('layouts.admin')

@section('title', __('admin.messages.title'))

@section('content')
    <div class="a-head">
        <h1 class="a-title">{{ $message->name }}</h1>
        <a class="btn btn--ghost btn--sm" href="{{ route('admin.messages.index', $message->archived_at ? ['box' => 'archived'] : []) }}">{{ __('admin.common.back') }}</a>
    </div>

    <article class="panel">
        <dl class="meta-list">
            <div><dt>{{ __('contact.fields.organization', [], 'ar') }}</dt><dd>{{ $message->organization ?: '—' }}</dd></div>
            <div><dt>{{ __('contact.fields.email', [], 'ar') }}</dt><dd><a href="mailto:{{ $message->email }}" dir="ltr">{{ $message->email }}</a></dd></div>
            <div><dt>{{ __('admin.messages.type') }}</dt><dd>{{ $message->inquiryLabel() }}</dd></div>
            <div><dt>{{ __('admin.messages.language') }}</dt><dd>{{ $message->locale === 'en' ? 'English' : 'العربية' }}</dd></div>
            <div><dt>{{ __('admin.messages.date') }}</dt><dd>{{ $message->created_at->format('Y-m-d H:i') }}</dd></div>
            <div><dt>{{ __('admin.messages.ip') }}</dt><dd dir="ltr">{{ $message->ip_address }}</dd></div>
        </dl>
        <div class="message-body" dir="auto">{{ $message->message }}</div>

        <div class="form-actions">
            <a class="btn btn--primary btn--sm" href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.$message->inquiryLabel($message->locale)) }}"><x-icon name="mail" /> {{ __('admin.messages.reply') }}</a>
            @if ($message->archived_at)
                <form method="POST" action="{{ route('admin.messages.unarchive', $message) }}" class="inline-form">
                    @csrf
                    <button class="btn btn--outline btn--sm" type="submit"><x-icon name="archive-restore" /> {{ __('admin.messages.unarchive') }}</button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.messages.archive', $message) }}" class="inline-form">
                    @csrf
                    <button class="btn btn--outline btn--sm" type="submit"><x-icon name="archive" /> {{ __('admin.messages.archive') }}</button>
                </form>
            @endif
            <x-admin.delete :action="route('admin.messages.destroy', $message)" />
        </div>
    </article>
@endsection
