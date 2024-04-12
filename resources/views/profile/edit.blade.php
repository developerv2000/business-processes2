@extends('layouts.app', ['page' => 'profile-edit'])

@section('main')
    <div class="pre-content pre-content--intended styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('My profile'), __('Edit')],
            'fullScreen' => false,
        ])
    </div>

    {{-- Profile update --}}
    <x-forms.template.edit-template action="{{ route('profile.update') }}">
        <div class="form__section">
            <h1 class="form__title main-title">{{ __('Personal data') }}</h1>

            <x-forms.inputs.instance-edit-input label="{{ __('Name') }}" name="name" type="text" :instance="$user" required />
            <x-forms.inputs.instance-edit-input label="{{ __('Email address') }}" name="email" type="email" :instance="$user" required />
            <x-forms.inputs.default-input label="{{ __('Photo') }}" name="photo" type="file" accept=".png, .jpg, .jpeg" />
        </div>
    </x-forms.template.edit-template>

    {{-- Password Update --}}
    <x-forms.template.edit-template class="update-password-form" id="update-password-form" action="{{ route('profile.update-password') }}">
        <div class="form__section">
            <h1 class="form__title main-title">{{ __('Password') }}</h1>

            <x-forms.inputs.default-input label="{{ __('Current password') }}" name="current_password" type="password" autocomplete="current-password" minlength="4" required />
            <x-forms.inputs.default-input label="{{ __('New password') }}" name="new_password" type="password" autocomplete="new_password" minlength="4" required />
        </div>
    </x-forms.template.edit-template>
@endsection
