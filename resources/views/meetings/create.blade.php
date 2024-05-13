@extends('layouts.app', ['page' => 'meetings-create'])

@section('main')
    <div class="pre-content pre-content--intended styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Meetings'), __('Create new')],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="create-form">{{ __('Store') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.create-template action="{{ route('meetings.store') }}">
        <div class="form__section">
            <x-forms.id-based-single-select.default-select
                label="Manufacturer"
                name="manufacturer_id"
                :options="$manufacturers"
                required />

            <x-forms.input.default-input
                label="Year"
                name="year"
                type="number" />

            <x-forms.input.default-input
                label="Who met"
                name="who_met" />
        </div>

        <div class="form__section">
            <x-forms.textarea.default-textarea
                label="Plan"
                name="plan" />

            <x-forms.textarea.default-textarea
                label="Topic"
                name="topic" />

            <x-forms.textarea.default-textarea
                label="Result"
                name="result" />

            <x-forms.textarea.default-textarea
                label="Outside the exhibition"
                name="outside_the_exhibition" />
        </div>
    </x-forms.template.create-template>
@endsection
