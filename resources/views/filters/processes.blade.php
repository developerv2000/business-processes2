@extends('filters.template')

@section('elements')
    <x-forms.date-range-input.request-based-input
        label="Status date"
        name="status_update_date" />

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
