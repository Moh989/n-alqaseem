@extends('layouts.admin')

@section('title', __('admin.dashboard.title'))

@section('content')
    <h1 class="a-title">{{ __('admin.dashboard.title') }}</h1>

    <div class="stat-grid">
        <a class="stat" href="{{ route('admin.messages.index') }}">
            <x-icon name="inbox" /><strong>{{ $unreadCount }}</strong><span>{{ __('admin.dashboard.unread') }}</span>
        </a>
        <a class="stat" href="{{ route('admin.services.index') }}">
            <x-icon name="briefcase" /><strong>{{ $servicesCount }}</strong><span>{{ __('admin.dashboard.published_services') }}</span>
        </a>
        <div class="stat stat--warn">
            <x-icon name="circle-alert" /><strong>{{ $pending->count() }}</strong><span>{{ __('admin.dashboard.pending_title') }}</span>
        </div>
    </div>

    <div class="a-grid a-grid--2">
        <section class="panel" aria-labelledby="pending-title">
            <h2 class="panel__title" id="pending-title">{{ __('admin.dashboard.pending_title') }}</h2>
            <p class="panel__help">{{ __('admin.dashboard.pending_help') }}</p>
            @if ($pending->isEmpty())
                <p class="muted">{{ __('admin.dashboard.no_pending') }}</p>
            @else
                <ul class="pending-list">
                    @foreach ($pending as $item)
                        <li><a href="{{ $item['url'] }}"><x-admin.pending-badge /> {{ $item['label'] }}</a></li>
                    @endforeach
                </ul>
            @endif
        </section>

        <div class="stack">
            <section class="panel" aria-labelledby="messages-title">
                <h2 class="panel__title" id="messages-title">{{ __('admin.dashboard.recent_messages') }}</h2>
                @forelse ($recentMessages as $message)
                    <a class="message-row {{ $message->read_at ? '' : 'is-unread' }}" href="{{ route('admin.messages.show', $message) }}">
                        <strong>{{ $message->name }}</strong>
                        <span>{{ $message->inquiryLabel() }}</span>
                        <time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->format('Y-m-d H:i') }}</time>
                    </a>
                @empty
                    <p class="muted">{{ __('admin.dashboard.no_messages') }}</p>
                @endforelse
            </section>

            <section class="panel" aria-labelledby="activity-title">
                <h2 class="panel__title" id="activity-title">{{ __('admin.dashboard.recent_activity') }}</h2>
                <ul class="activity">
                    @foreach ($activity as $log)
                        <li>
                            <code>{{ $log->action }}</code>
                            <span>{{ $log->summary }}</span>
                            <small>{{ $log->user?->name }} · {{ $log->created_at?->format('Y-m-d H:i') }}</small>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>
    </div>
@endsection
