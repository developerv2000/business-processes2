@extends('filters.template')

@section('elements')
    {{-- Notify if redirected from statistics index page & was_active_on_requested_month_and_year requested --}}
    @if ($request->was_active_on_requested_month_and_year)
        <x-forms.input.request-based-input
            type="text"
            label="Special filter"
            name="special_filter"
            :value="__('Was active on') . ' ' . str_pad($request->was_active_on_month, 2, '0', STR_PAD_LEFT) . '.' . $request->was_active_on_year"
            readonly />

        <input type="hidden" name="was_active_on_requested_month_and_year" value="1">
        <input type="hidden" name="was_active_on_year" value="{{ $request->was_active_on_year }}">
        <input type="hidden" name="was_active_on_month" value="{{ $request->was_active_on_month }}">
    @endif

    <x-forms.id-based-single-select.request-based-select
        label="Analyst"
        name="analyst_user_id"
        :options="$analystUsers" />

    <x-forms.id-based-single-select.request-based-select
        label="BDM"
        name="bdm_user_id"
        :options="$bdmUsers" />

    <x-forms.id-based-multiple-select.request-based-select
        label="Country"
        name="country_id[]"
        :options="$countries" />

    <x-forms.single-select.request-based-select
        label="Manufacturer countries"
        name="specific_manufacturer_country"
        :options="$specificManufacturerCountries" />

    <x-forms.id-based-multiple-select.request-based-select
        label="Manufacturer"
        name="id[]"
        :options="$manufacturers" />

    <x-forms.id-based-single-select.request-based-select
        label="Category"
        name="category_id"
        :options="$categories" />

    <x-forms.boolean-select.request-based-select
        label="Status"
        name="is_active"
        true-option-label="Active"
        false-option-label="Stoped" />

    <x-forms.id-based-multiple-select.request-based-select
        label="Product class"
        name="productClasses[]"
        :options="$productClasses" />

    <x-forms.id-based-multiple-select.request-based-select
        label="Zones"
        name="zones[]"
        :options="$zones" />

    <x-forms.boolean-select.request-based-select
        label="Important"
        name="is_important" />

    <x-forms.id-based-single-select.request-based-select
        label="Process search country"
        name="country_code_id"
        :options="$countryCodes" />

    <x-forms.id-based-multiple-select.request-based-select
        label="Black list"
        name="blacklists[]"
        :options="$blacklists" />

    @include('filters.partials.default-elements', [
        'includeIdInput' => false,
    ])
@endsection
