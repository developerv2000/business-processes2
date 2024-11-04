@extends('filters.template')

@section('elements')
    <x-forms.id-based-multiple-select.request-based-select
        label="Order"
        name="order_id[]"
        :options="$namedOrders"
        option-caption-attribute="purchase_order_name" />

    <x-forms.multiple-select.request-based-select
        label="Brand name ENG"
        name="fixed_trademark_en_for_order[]"
        :options="$fixedEnTrademarks" />

    <x-forms.multiple-select.request-based-select
        label="Brand name RUS"
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

    <x-forms.input.request-based-input
        type="number"
        label="Quantity"
        name="quantity" />

    <x-forms.input.request-based-input
        type="number"
        step="0.01"
        label="Price"
        name="price" />

    <x-forms.boolean-select.request-based-select
        label="Confirmed"
        name="is_confirmed" />

    @include('filters.partials.default-elements', [
        'includeIdInput' => true,
    ])
@endsection