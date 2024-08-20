@extends('layouts.app', ['page' => 'plan-edit'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('SPG'), __('Edit'), $instance->year],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.edit-template action="{{ route('plan.update', $instance->id) }}">
        <div class="form__section">
            <x-forms.input.instance-edit-input
                label="Year"
                name="year"
                type="number"
                :instance="$instance"
                required />
        </div>

        @include('comments.model-form-partials.edit-form-fields')
    </x-forms.template.edit-template>
@endsection
