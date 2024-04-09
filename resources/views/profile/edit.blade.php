@extends('layouts.app', ['page' => 'profile-edit'])

@section('main')
    <div class="pre-content pre-content--intended styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('My profile'), __('Edit')],
            'fullScreen' => false,
        ])
    </div>

    {{-- Profile update --}}
    <x-form.edit-template action="{{ route('profile.update') }}">
        <div class="form__section">
            <h1 class="form__title main-title">{{ __('Personal data') }}</h1>

            <x-form.grouped-elements.input label="{{ __('Name') }}" name="name" type="text" editing="1" :object="$user" required />
            <x-form.grouped-elements.input label="{{ __('Email address') }}" name="email" type="email" editing="1" :object="$user" required />
            <x-form.grouped-elements.input label="{{ __('Photo') }}" name="photo" type="file" accept=".png, .jpg, .jpeg" />
        </div>
    </x-form.edit-template>

    {{-- Password Update --}}
    <form class='form edit-form update-password-form' action="{{ route('profile.update-password') }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="form__section">
            <h1 class="form__title main-title">{{ __('Password') }}</h1>

            <x-form.grouped-elements.input label="{{ __('Current password') }}" name="current_password" type="password" autocomplete="current-password" minlength="4" required />
            <x-form.grouped-elements.input label="{{ __('New password') }}" name="new_password" type="password" autocomplete="new_password" minlength="4" required />
        </div>

        <x-different.button class="form__submit" type="submit">{{ __('Update') }}</x-different.button>
    </form>
@endsection
