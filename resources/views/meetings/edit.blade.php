@extends('layouts.app', ['page' => 'meetings-edit'])

@section('main')
    <div class="pre-content pre-content--intended styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Meetings'), __('Edit'), '# ' . $instance->id],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.edit-template action="{{ route('meetings.update', $instance->id) }}">
        <div class="form__section">
            <x-forms.id-based-single-select.instance-edit-select
                label="Manufacturer"
                name="manufacturer_id"
                :options="$manufacturers"
                :instance="$instance"
                required />

            <x-forms.input.instance-edit-input
                label="Year"
                name="year"
                type="number"
                :instance="$instance" />

            <x-forms.input.instance-edit-input
                label="Who met"
                name="who_met"
                :instance="$instance" />
        </div>

        <div class="form__section">
            <x-forms.textarea.instance-edit-textarea
                label="Plan"
                name="plan"
                :instance="$instance" />

            <x-forms.textarea.instance-edit-textarea
                label="Topic"
                name="topic"
                :instance="$instance" />

            <x-forms.textarea.instance-edit-textarea
                label="Result"
                name="result"
                :instance="$instance" />

            <x-forms.textarea.instance-edit-textarea
                label="Outside the exhibition"
                name="outside_the_exhibition"
                :instance="$instance" />
        </div>
    </x-forms.template.edit-template>
@endsection
