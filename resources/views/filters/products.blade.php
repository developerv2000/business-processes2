@extends('filters.template')

@section('elements')
    <x-forms.id-based-multiple-select.request-based-select
        label="Generic"
        name="inn_id[]"
        :options="$inns" />

    <x-forms.id-based-multiple-select.request-based-select
        label="Form"
        name="form_id[]"
        :options="$productForms" />

    <x-forms.input.request-based-input
        type="text"
        label="Dosage"
        name="dosage" />

    <x-forms.input.request-based-input
        type="text"
        label="Pack"
        name="pack" />

    <x-forms.id-based-multiple-select.request-based-select
        label="Country"
        name="country_id[]"
        :options="$countries" />

    <x-forms.id-based-multiple-select.request-based-select
        label="Manufacturer"
        name="manufacturer_id[]"
        :options="$manufacturers" />

    <x-forms.id-based-single-select.request-based-select
        label="Category"
        name="manufacturer_category_id"
        :options="$manufacturerCategories" />

    <x-forms.id-based-multiple-select.request-based-select
        label="Product class"
        name="class_id[]"
        :options="$productClasses" />

    <x-forms.id-based-single-select.request-based-select
        label="Analyst"
        name="analyst_user_id"
        :options="$analystUsers" />

    <x-forms.id-based-single-select.request-based-select
        label="BDM"
        name="bdm_user_id"
        :options="$bdmUsers" />

    <x-forms.multiple-select.request-based-select
        label="Brand"
        name="brand[]"
        :options="$brands" />

    <x-forms.id-based-multiple-select.request-based-select
        label="Shelf life"
        name="shelf_life_id[]"
        :options="$shelfLifes" />

    <x-forms.id-based-multiple-select.request-based-select
        label="Zones"
        name="zones[]"
        :options="$zones" />

    @include('filters.partials.default-elements', [
        'includeIdInput' => true,
    ])
@endsection
