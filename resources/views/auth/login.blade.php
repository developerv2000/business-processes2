@extends('auth.app', ['page' => 'login-page'])

@section('main')
    <div class="auth-box styled-box">
        <x-different.logo class="auth-box__logo" theme="light" />
        <h1 class="auth-box__title main-title">{{ __('Account Login') }}</h1>

        <form class="form login-form" action="/login" method="POST">
            @csrf

            <x-forms.input.default-input label="{{ __('Email address') }}" name="email" type="email" autofocus required />
            <x-forms.input.default-input label="{{ __('Password') }}" name="password" type="password" autocomplete="current-password" minlength="4" required />
            <x-different.button>{{ __('Log in') }}</x-different.button>
        </form>
    </div>
@endsection
