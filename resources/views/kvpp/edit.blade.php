@extends('layouts.app', ['page' => 'kvpp-edit'])

@section('main')
    <div class="pre-content pre-content--intended styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('KVPP'), __('Edit'), '# ' . $instance->id],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.edit-template action="{{ route('kvpp.update', $instance->id) }}">
        <div class="form__section">
            <x-forms.id-based-single-select.instance-edit-select
                label="Status"
                name="status_id"
                :options="$statuses"
                :instance="$instance"
                required />

            <x-forms.id-based-single-select.instance-edit-select
                label="Priority"
                name="priority_id"
                :options="$priorities"
                :instance="$instance"
                required />

            <x-forms.id-based-single-select.instance-edit-select
                label="Source"
                name="source_id"
                :options="$sources"
                :instance="$instance"
                required />
        </div>

        <div class="form__section">
            <x-forms.id-based-single-select.instance-edit-select
                label="Country"
                name="country_code_id"
                :options="$countryCodes"
                :instance="$instance"
                required />

            <x-forms.id-based-single-select.instance-edit-select
                label="Generic"
                name="inn_id"
                :options="$inns"
                :instance="$instance"
                required />

            <x-forms.id-based-single-select.instance-edit-select
                label="Form"
                name="form_id"
                :options="$productForms"
                :instance="$instance"
                required />

            <x-forms.input.instance-edit-input
                label="Dosage"
                name="dosage"
                :instance="$instance"
                data-on-input="validate-specific-input" />

            <x-forms.input.instance-edit-input
                label="Pack"
                name="pack"
                :instance="$instance"
                data-on-input="validate-specific-input" />
        </div>

        {{-- Empty container used to hold similar kvpps after ajax request --}}
        <div class="form__section similar-records"></div>

        <div class="form__section">
            <x-forms.id-based-single-select.instance-edit-select
                label="MAH"
                name="marketing_authorization_holder_id"
                :options="$marketingAuthorizationHolders"
                :instance="$instance" />

            <x-forms.input.instance-edit-input
                label="Information"
                name="information"
                :instance="$instance" />

            <x-forms.id-based-single-select.instance-edit-select
                label="Portfolio manager"
                name="portfolio_manager_id"
                :options="$portfolioManagers"
                :instance="$instance" />

            <x-forms.id-based-single-select.instance-edit-select
                label="Analyst"
                name="analyst_user_id"
                :options="$analystUsers"
                :instance="$instance" />
        </div>

        <div class="form__section">
            <x-forms.input.instance-edit-input
                label="Date of forecast"
                name="date_of_forecast"
                type="datetime-local"
                :instance="$instance" />

            <x-forms.input.instance-edit-input
                label="Forecast 1 year"
                name="forecast_year_1"
                type="number"
                :instance="$instance" />

            <x-forms.input.instance-edit-input
                label="Forecast 2 year"
                name="forecast_year_2"
                type="number"
                :instance="$instance" />

            <x-forms.input.instance-edit-input
                label="Forecast 3 year"
                name="forecast_year_3"
                type="number"
                :instance="$instance" />
        </div>

        @include('comments.model-form-partials.edit-form-fields')
    </x-forms.template.edit-template>
@endsection
