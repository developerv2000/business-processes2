@extends('filters.template')

@section('elements')
    <x-forms.multiple-select.request-based-select
        label="Brand name ENG"
        name="fixed_trademark_en_for_order[]"
        :options="$fixedEnTrademarks" />

    <x-forms.multiple-select.request-based-select
        label="Brand name ENG"
        name="fixed_trademark_ru_for_order[]"
        :options="$fixedRuTrademarks" />

    <x-forms.id-based-multiple-select.request-based-select
        label="Manufacturer"
        name="manufacturer_id[]"
        :options="$manufacturers" />

    <x-forms.id-based-multiple-select.request-based-select
        label="Country"
        name="country_code_id[]"
        :options="$countryCodes" />

    <x-forms.id-based-multiple-select.request-based-select
        label="MAH"
        name="marketing_authorization_holder_id[]"
        :options="$marketingAuthorizationHolders" />

    <x-forms.multiple-select.request-based-select
        label="VPS Brand Eng"
        name="trademark_en[]"
        :options="$enTrademarks" />

    <x-forms.multiple-select.request-based-select
        label="VPS Brand Rus"
        name="trademark_ru[]"
        :options="$ruTrademarks" />

    <x-forms.id-based-multiple-select.request-based-select
        label="Form"
        name="form_id[]"
        :options="$productForms" />

    <x-forms.input.request-based-input
        type="text"
        label="Pack"
        name="pack" />

    @include('filters.partials.default-elements', [
        'includeIdInput' => true,
    ])
@endsection
