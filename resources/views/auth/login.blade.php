@extends('auth.app', ['page' => 'login-page'])

@section('main')
    <div class="auth-box styled-box">
        <x-different.logo class="auth-box__logo" />
        <h1 class="auth-box__title main-title">{{ __('Account Login') }}</h1>

        <form class="form login-form" action="/login" method="POST">
            @csrf

            <x-form.groups.validateable label="{{ __('Email address') }}" error-name="email" required="true">
                <x-form.default-elements.input name="email" type="email" autofocus required />
            </x-form.groups.validateable>

            <x-form.groups.validateable label="{{ __('Password') }}" error-name="password" required="true">
                <x-form.default-elements.input name="password" type="password" autocomplete="current-password" minlength="4" required />
            </x-form.groups.validateable>

            <x-different.button>{{ __('Log in') }}</x-different.button>
        </form>
    </div>
@endsection
