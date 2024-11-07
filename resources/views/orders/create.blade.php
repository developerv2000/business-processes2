@extends('layouts.app', ['page' => 'orders-create'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Orders'), __('Create new')],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="create-form">{{ __('Store') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.create-template action="{{ route('orders.store') }}">
        <div class="form__section form__section--horizontal orders-create__form-order-section">
            <x-forms.id-based-single-select.default-select
                label="Manufacturer"
                name="manufacturer_id"
                :options="$manufacturers"
                required />

            <x-forms.id-based-single-select.default-select
                label="Country"
                name="country_code_id"
                :options="$countryCodes"
                required />

            <x-forms.input.default-input
                label="PO №"
                name="purchase_order_name" />

            <x-forms.input.default-input
                type="date"
                label="PO date"
                name="purchase_order_date" />

            <x-forms.id-based-single-select.default-select
                label="Currency"
                name="currency_id"
                :options="$currencies"
                :default-value="$defaultCurrency->id"
                required />

            <x-forms.input.default-input
                type="date"
                label="Receive date"
                name="receive_date" />

            <x-forms.input.default-input
                type="date"
                label="Readiness date"
                name="readiness_date" />

            <x-forms.input.default-input
                type="date"
                label="Expected dispatch date"
                name="expected_dispatch_date" />
        </div>

        <div class="orders-create__products-section styled-box">
            <h2 class="orders-create__products-title main-title">Products list</h2>

            <div class="orders-create__products-list"></div>

            <x-different.button type="button" class="orders-create__add-product-btn" style="action" icon="add" type="button">
                {{ __('Add product') }}
            </x-different.button>
        </div>
    </x-forms.template.create-template>
@endsection
