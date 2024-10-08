@extends('layouts.app', ['page' => 'users-create'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Users'), __('Create new')],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="create-form">{{ __('Store') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.create-template action="{{ route('users.store') }}">
        <div class="form__section">
            <x-forms.input.default-input
                label="Name"
                name="name"
                required />

            <x-forms.input.default-input
                label="Email address"
                name="email"
                type="email"
                required />

            <x-forms.input.default-input
                label="Photo"
                name="photo"
                type="file"
                accept=".png, .jpg, .jpeg"
                required />
        </div>

        <div class="form__section">
            <x-forms.id-based-multiple-select.default-select
                label="Roles"
                name="roles[]"
                :options="$roles"
                required />

            <x-forms.id-based-multiple-select.default-select
                label="Permissions"
                name="permissions[]"
                :options="$permissions" />

            <x-forms.id-based-multiple-select.default-select
                label="Responsible countries"
                name="responsibleCountries[]"
                :options="$countries" />

            <x-forms.input.default-input
                label="Password"
                name="password"
                type="password"
                minlength="4"
                autocomplete="new-password"
                required />
        </div>
    </x-forms.template.create-template>
@endsection
