@extends('filters.template')

@section('elements')
    <x-forms.id-based-single-select.request-based-select
        label="Country"
        name="country_code_id"
        :options="$countryCodes" />

    <x-forms.boolean-select.request-based-select
        label="Source EU"
        name="source_eu" />

    <x-forms.boolean-select.request-based-select
        label="Source IN"
        name="source_in" />

    <x-forms.boolean-select.request-based-select
        label="Status"
        name="is_active"
        true-option-label="Active"
        false-option-label="Stoped" />

    <x-forms.id-based-single-select.request-based-select
        label="Priority"
        name="priority_id"
        :options="$priorities" />

    <x-forms.id-based-single-select.request-based-select
        label="Generic"
        name="inn_id"
        :options="$inns" />

    <x-forms.id-based-single-select.request-based-select
        label="Form"
        name="form_id"
        :options="$productForms" />

    <x-forms.input.request-based-input
        type="text"
        label="Dosage"
        name="dosage" />

    <x-forms.input.request-based-input
        type="text"
        label="Pack"
        name="pack" />

    <x-forms.id-based-single-select.request-based-select
        label="PC"
        name="marketing_authorization_holder_id"
        :options="$marketingAuthorizationHolders" />

    <x-forms.id-based-single-select.request-based-select
        label="Portfolio manager"
        name="portfolio_manager_id"
        :options="$portfolioManagers" />

    <x-forms.id-based-single-select.request-based-select
        label="Analyst"
        name="analyst_user_id"
        :options="$analystUsers" />

    @include('filters.partials.default-elements', [
        'includeIdInput' => true,
    ])
@endsection
