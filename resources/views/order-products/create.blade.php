@extends('layouts.app', ['page' => 'order-products-create'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Orders'), $order->purchase_order_name ?: '# ' . $order->id, __('Create new product')],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="create-form">{{ __('Store') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.create-template action="{{ route('order.products.store') }}">
        <input type="hidden" name="order_id" value="{{ $order->id }}">

        <div class="form__section">
            <x-forms.id-based-single-select.default-select
                label="Brand name ENG"
                name="process_id"
                :options="$processes"
                optionCaptionAttribute="fixed_trademark_en_for_order"
                required />

            <x-forms.id-based-single-select.default-select
                label="Country"
                name="country_code_id"
                :options="$countryCodes"
                required />

            <x-forms.id-based-single-select.default-select
                label="MAH"
                name="marketing_authorization_holder_id"
                :options="$marketingAuthorizationHolders"
                required />

            <x-forms.input.default-input
                label="Quantity"
                name="quantity"
                type="number" />

            <x-forms.input.default-input
                type="number"
                step="0.01"
                label="Price"
                name="price"
                required />
        </div>

        @include('comments.model-form-partials.create-form-fields')
    </x-forms.template.create-template>
@endsection
