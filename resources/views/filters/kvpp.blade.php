@extends('filters.template')

@section('elements')
    <x-forms.id-based-single-select.request-based-select
        label="Country"
        name="country_code_id"
        :options="$countryCodes" />

    <x-forms.id-based-single-select.request-based-select
        label="Priority"
        name="priority_id"
        :options="$priorities" />

    <x-forms.id-based-single-select.request-based-select
        label="Source"
        name="source_id"
        :options="$sources" />

    <x-forms.id-based-single-select.request-based-select
        label="Generic"
        name="inn_id"
        :options="$inns" />

    <x-forms.id-based-single-select.request-based-select
        label="Form"
        name="form_id"
        :options="$forms" />

    <x-forms.input.request-based-input
        type="text"
        label="Dosage"
        name="dosage" />

    <x-forms.input.request-based-input
        type="text"
        label="Pack"
        name="pack" />

    <x-forms.id-based-single-select.request-based-select
        label="MAH"
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
