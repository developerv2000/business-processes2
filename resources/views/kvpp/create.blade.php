@extends('layouts.app', ['page' => 'kvpp-create'])

@section('main')
    <div class="pre-content pre-content--intended styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('KVPP'), __('Create new')],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="create-form">{{ __('Store') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.create-template action="{{ route('kvpp.store') }}">
        <div class="form__section">
            <x-forms.id-based-single-select.default-select
                label="Status"
                name="status_id"
                :options="$statuses"
                required />

            <x-forms.id-based-single-select.default-select
                label="Priority"
                name="priority_id"
                :options="$priorities"
                required />

            <x-forms.id-based-single-select.default-select
                label="Source"
                name="source_id"
                :options="$sources"
                required />
        </div>

        <div class="form__section">
            <x-forms.id-based-single-select.default-select
                label="Country"
                name="country_code_id"
                :options="$countryCodes"
                required />

            <x-forms.id-based-single-select.default-select
                label="Generic"
                name="inn_id"
                :options="$inns"
                required />

            <x-forms.id-based-single-select.default-select
                label="Form"
                name="form_id"
                :options="$productForms"
                required />

            <x-forms.input.default-input
                label="Dosage"
                name="dosage"
                data-on-input="validate-specific-input" />

            <x-forms.input.default-input
                label="Pack"
                name="pack"
                data-on-input="validate-specific-input" />
        </div>

        {{-- Empty container used to hold similar kvpps after ajax request --}}
        <div class="form__section similar-records"></div>

        <div class="form__section">
            <x-forms.id-based-multiple-select.default-select
                label="MAH"
                name="marketing_authorization_holder_ids[]"
                :options="$marketingAuthorizationHolders"
                required />

            <x-forms.input.default-input
                label="Information"
                name="information" />

            <x-forms.id-based-single-select.default-select
                label="Portfolio manager"
                name="portfolio_manager_id"
                :options="$portfolioManagers" />

            <x-forms.id-based-single-select.default-select
                label="Analyst"
                name="analyst_user_id"
                :options="$analystUsers" />
        </div>

        <div class="form__section">
            <x-forms.input.default-input
                label="Date of forecast"
                name="date_of_forecast"
                type="date" />

            <x-forms.input.default-input
                label="Forecast 1 year"
                name="forecast_year_1"
                type="number" />

            <x-forms.input.default-input
                label="Forecast 2 year"
                name="forecast_year_2"
                type="number" />

            <x-forms.input.default-input
                label="Forecast 3 year"
                name="forecast_year_3"
                type="number" />
        </div>

        @include('comments.model-form-partials.create-form-fields')
    </x-forms.template.create-template>
@endsection
