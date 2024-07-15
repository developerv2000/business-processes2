@extends('filters.template')

@section('elements')
    <x-forms.date-range-input.request-based-input
        label="Status date"
        name="status_update_date" />

    {{-- Notify if redirected from statistics index page & stage 5 (Kk) requested --}}
    @if ($request->contracted_on_requested_month_and_year)
        <x-forms.input.request-based-input
            type="text"
            label="Special filter"
            name="special_filter"
            :value="__('Contracted on') . ' ' . str_pad($request->contracted_month, 2, '0', STR_PAD_LEFT) . '.' . $request->contracted_year"
            readonly />

        <input type="hidden" name="contracted_on_requested_month_and_year" value="1">
        <input type="hidden" name="contracted_year" value="{{ $request->contracted_year }}">
        <input type="hidden" name="contracted_month" value="{{ $request->contracted_month }}">
    @endif

    {{-- Notify if redirected from statistics index page & has_status_history requested --}}
    @if ($request->has_status_history)
        <x-forms.input.request-based-input
            type="text"
            label="Special filter"
            name="special_filter"
            :value="__('Has status history')"
            readonly />

        <input type="hidden" name="has_status_history" value="1">
        <input type="hidden" name="has_status_history_on_month" value="{{ $request->has_status_history_on_month }}">
        <input type="hidden" name="has_status_history_on_year" value="{{ $request->has_status_history_on_year }}">

        @if ($request->has_status_history_based_on_id)
            <input type="hidden" name="has_status_history_based_on_id" value="1">
            <input type="hidden" name="has_status_history_general_status_id" value="{{ $request->has_status_history_general_status_id }}">
        @elseif ($request->has_status_history_based_on_name_for_analysts)
            <input type="hidden" name="has_status_history_based_on_name_for_analysts" value="1">
            <input type="hidden" name="has_status_history_general_status_name_for_analysts" value="{{ $request->has_status_history_general_status_name_for_analysts }}">
        @endif
    @endif

    <x-forms.id-based-single-select.request-based-select
        label="Search country"
        name="country_code_id"
        :options="$countryCodes" />

    <x-forms.id-based-single-select.request-based-select
        label="Manufacturer"
        name="manufacturer_id"
        :options="$manufacturers" />

    <x-forms.id-based-single-select.request-based-select
        label="Product status"
        name="status_id"
        :options="$statuses" />

    <x-forms.single-select.request-based-select
        label="Product status An*"
        name="name_for_analysts"
        :options="$generalStatusNamesForAnalysts" />

    <x-forms.id-based-single-select.request-based-select
        label="General status"
        name="general_status_id"
        :options="$generalStatuses" />

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

    @if ($request->user()->isAdminOrModerator())
        <x-forms.id-based-single-select.request-based-select
            label="Analyst"
            name="analyst_user_id"
            :options="$analystUsers" />
    @endif

    <x-forms.id-based-single-select.request-based-select
        label="BDM"
        name="bdm_user_id"
        :options="$bdmUsers" />

    <x-forms.id-based-multiple-select.request-based-select
        label="Responsible"
        name="responsiblePeople[]"
        :options="$responsiblePeople" />

    <x-forms.id-based-single-select.request-based-select
        label="Manufacturer country"
        name="country_id"
        :options="$countries" />

    <x-forms.single-select.request-based-select
        label="Manufacturer countries"
        name="specific_manufacturer_country"
        :options="$specificManufacturerCountries" />

    <x-forms.id-based-single-select.request-based-select
        label="MAH"
        name="marketing_authorization_holder_id"
        :options="$marketingAuthorizationHolders" />

    <x-forms.input.request-based-input
        type="text"
        label="Brand Eng"
        name="trademark_en" />

    <x-forms.input.request-based-input
        type="text"
        label="Brand Rus"
        name="trademark_ru" />

    <x-forms.id-based-single-select.request-based-select
        label="Product class"
        name="product_class_id"
        :options="$productClasses" />

    <x-forms.id-based-single-select.request-based-select
        label="Manufacturer category"
        name="manufacturer_category_id"
        :options="$manufacturerCategories" />

    @include('filters.partials.default-elements', [
        'includeIdInput' => true,
    ])
@endsection
