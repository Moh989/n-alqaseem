@extends('layouts.admin')

@section('title', __('admin.account.title'))

@section('content')
    <h1 class="a-title">{{ __('admin.account.title') }}</h1>
    <form class="panel stack a-narrow" method="POST" action="{{ route('admin.account.update') }}">
        @csrf
        @method('PUT')
        <p><strong>{{ $user->name }}</strong> — <span dir="ltr">{{ $user->email }}</span></p>
        <div class="a-field">
            <label class="a-field__label" for="current_password">{{ __('admin.account.current_password') }}</label>
            <input class="a-input" id="current_password" name="current_password" type="password" required autocomplete="current-password" dir="ltr">
        </div>
        <div class="a-field">
            <label class="a-field__label" for="password">{{ __('admin.account.new_password') }}</label>
            <input class="a-input" id="password" name="password" type="password" required autocomplete="new-password" dir="ltr" minlength="12" aria-describedby="password-help">
            <p class="a-field__help" id="password-help">{{ __('admin.account.password_help') }}</p>
        </div>
        <div class="a-field">
            <label class="a-field__label" for="password_confirmation">{{ __('admin.account.confirm_password') }}</label>
            <input class="a-input" id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" dir="ltr">
        </div>
        <div class="form-actions">
            <button class="btn btn--primary" type="submit"><x-icon name="save" /> {{ __('admin.common.save') }}</button>
        </div>
    </form>
@endsection
