@extends('filters.template')

@section('elements')
    <x-forms.id-based-single-select.request-based-select
        label="Analyst"
        name="analyst_user_id"
        :options="$analystUsers"
    />

    <x-forms.id-based-single-select.request-based-select
        label="Bdm"
        name="bdm_user_id"
        :options="$bdmUsers"
    />

    <x-forms.id-based-single-select.request-based-select
        label="Country"
        name="country_id"
        :options="$countries"
    />

    <x-forms.id-based-single-select.request-based-select
        label="Manufacturer"
        name="id"
        :options="$manufacturers"
    />

    <x-forms.id-based-single-select.request-based-select
        label="Category"
        name="category_id"
        :options="$categories"
    />

    <x-forms.boolean-select.request-based-select
        label="Status"
        name="is_active"
        true-option-label="Active"
        false-option-label="Stoped"
    />

    <x-forms.id-based-multiple-select.request-based-select
        label="Product category"
        name="productClasses[]"
        :options="$productClasses"
    />

    <x-forms.id-based-multiple-select.request-based-select
        label="Zones"
        name="zones[]"
        :options="$zones"
    />

    <x-forms.boolean-select.request-based-select
        label="Important"
        name="is_important"
    />

    <x-forms.id-based-multiple-select.request-based-select
        label="Black list"
        name="blacklists[]"
        :options="$blacklists"
    />

    @include('filters.partials.default-elements', [
        'includeIdInput' => false
    ])
@endsection

