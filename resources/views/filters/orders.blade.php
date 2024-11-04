@extends('filters.template')

@section('elements')
    <x-forms.id-based-multiple-select.request-based-select
        label="Order"
        name="id[]"
        :options="$namedOrders"
        option-caption-attribute="purchase_order_name" />

    <x-forms.id-based-multiple-select.request-based-select
        label="Manufacturer"
        name="manufacturer_id[]"
        :options="$manufacturers" />

    <x-forms.boolean-select.request-based-select
        label="Confirmed"
        name="is_confirmed" />

    <x-forms.date-range-input.request-based-input
        label="Receive date"
        name="receive_date" />

    <x-forms.date-range-input.request-based-input
        label="PO date"
        name="purchase_order_date" />

    <x-forms.date-range-input.request-based-input
        label="Readiness date"
        name="readiness_date" />

    <x-forms.date-range-input.request-based-input
        label="Expected dispatch date"
        name="expected_dispatch_date" />

    @include('filters.partials.default-elements', [
        'includeIdInput' => true,
    ])
@endsection
