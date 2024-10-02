@extends('layouts.app', ['page' => 'users-edit'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Users'), __('Edit'), $instance->name],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="remove" data-click-action="show-modal" data-modal-selector=".single-delete-modal">{{ __('Delete') }}</x-different.button>
        </div>
    </div>

    <x-errors.single name="user_deletion" />

    {{-- Personal data --}}
    <x-forms.template.edit-template action="{{ route('users.update', $instance->id) }}">
        <div class="form__section">
            <h1 class="form__title main-title">{{ __('Personal data') }}</h1>

            <x-forms.input.instance-edit-input
                label="{{ __('Name') }}"
                name="name" type="text"
                :instance="$instance"
                required />

            <x-forms.input.instance-edit-input
                label="{{ __('Email address') }}"
                name="email" type="email"
                :instance="$instance"
                required />

            <x-forms.id-based-multiple-select.instance-edit-select
                label="Roles"
                name="roles[]"
                :options="$roles"
                :instance="$instance"
                required />

            <x-forms.id-based-multiple-select.instance-edit-select
                label="Responsible countries"
                name="responsibleCountries[]"
                :options="$countries"
                :instance="$instance" />

            <x-forms.input.default-input
                label="{{ __('Photo') }}"
                name="photo"
                type="file"
                accept=".png, .jpg, .jpeg" />
        </div>
    </x-forms.template.edit-template>

    {{-- Password --}}
    <x-forms.template.edit-template class="update-password-form" id="update-password-form" action="{{ route('users.update-password', $instance->id) }}">
        <div class="form__section">
            <h1 class="form__title main-title">{{ __('Password') }}</h1>

            {{-- used in UpdatePassword FormRequest, to differ from profile edit page --}}
            <input type="hidden" name="by_admin" value="1">

            <x-forms.input.default-input
                label="{{ __('New password') }}"
                name="new_password"
                type="password"
                autocomplete="new_password"
                minlength="4"
                required />
        </div>
    </x-forms.template.edit-template>

    <x-modals.single-delete action="{{ route('users.destroy') }}" :instance-id="$instance->id" :force-delete="false" />
@endsection
