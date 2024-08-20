@extends('layouts.app', ['page' => 'plan-create'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('SPG'), __('Create new')],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="create-form">{{ __('Store') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.create-template action="{{ route('plan.store') }}">
        <div class="form__section">
            <x-forms.input.default-input
                label="Year"
                name="year"
                type="number"
                required />
        </div>

        @include('comments.model-form-partials.create-form-fields')
    </x-forms.template.create-template>
@endsection
