@extends('layouts.app', ['page' => 'kvpp-create'])

@section('main')
    <div class="pre-content styled-box">
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

            <x-forms.radiogroup.default-radiogroup
                label="Source EU"
                name="source_eu"
                :options="$booleanOptions"
                default-value="0"
                required />

            <x-forms.radiogroup.default-radiogroup
                label="Source IN"
                name="source_in"
                :options="$booleanOptions"
                default-value="0"
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
                label="PC"
                name="marketing_authorization_holder_ids[]"
                :options="$marketingAuthorizationHolders"
                required />

            <x-forms.input.default-input
                label="Additional search info"
                name="additional_search_information" />

            <x-forms.id-based-multiple-select.default-select
                label="Additional search countries"
                name="additionalSearchCountries[]"
                :options="$countryCodes" />

            <x-forms.id-based-single-select.default-select
                label="Portfolio manager"
                name="portfolio_manager_id"
                :options="$portfolioManagers" />

            <x-forms.id-based-single-select.current-user-select
                label="Analyst"
                name="analyst_user_id"
                :options="$analystUsers" />
        </div>

        <div class="form__section">
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
