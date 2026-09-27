@props(['route', 'params'])
<div class="move-buttons">
    <form method="POST" action="{{ route($route, [...$params, 'direction' => 'up']) }}">
        @csrf
        <button class="icon-btn" type="submit" title="{{ __('admin.common.move_up') }}"><x-icon name="arrow-up" /><span class="visually-hidden">{{ __('admin.common.move_up') }}</span></button>
    </form>
    <form method="POST" action="{{ route($route, [...$params, 'direction' => 'down']) }}">
        @csrf
        <button class="icon-btn" type="submit" title="{{ __('admin.common.move_down') }}"><x-icon name="arrow-down" /><span class="visually-hidden">{{ __('admin.common.move_down') }}</span></button>
    </form>
</div>
