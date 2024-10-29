@extends('filters.template')

@section('elements')
    <x-forms.id-based-multiple-select.request-based-select
        label="PO №"
        name="id[]"
        :options="$applications" />

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
        label="Brand Eng"
        name="trademark_en[]"
        :options="$enTrademarks" />

    <x-forms.multiple-select.request-based-select
        label="Brand Rus"
        name="trademark_ru[]"
        :options="$ruTrademarks" />

    @include('filters.partials.default-elements', [
        'includeIdInput' => false,
    ])
@endsection
