@extends('layouts.app', ['page' => 'invoices-create-goods'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Invoices'), __('Create new goods')],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="create-form">{{ __('Store') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.create-template action="{{ route('invoices.store.goods') }}" submit-text="Load products">
        <div class="form__section">
            <div class="form__row">
                <x-forms.input.default-input
                    label="Invoice"
                    name="name"
                     />

                <x-forms.input.default-input
                    label="Date"
                    type="date"
                    name="date"
                     />

                <x-forms.id-based-single-select.default-select
                    label="Payer"
                    name="payer_id"
                    :options="$payers"
                    required />
            </div>

            <div class="form__row">
                <x-forms.id-based-single-select.default-select
                    label="Payment type"
                    name="payment_type_id"
                    :options="$paymentTypes"
                    required />

                <x-forms.id-based-single-select.default-select
                    label="Currency"
                    name="currency_id"
                    :options="$currencies"
                    :default-value="$defaultCurrency->id"
                    required />

                <x-forms.input.default-input
                    label="Payment refer"
                    name="group_name" />
            </div>

            <div class="form__row">
                <x-forms.id-based-multiple-select.default-select
                    label="Orders"
                    name="order_ids[]"
                    :options="$orders"
                    optionCaptionAttribute="label"
                    required />

                <div class="form-group"></div>
                <div class="form-group"></div>
            </div>
        </div>

        <div class="invoices-create-goods__products-list-wrapper"></div>
    </x-forms.template.create-template>
@endsection
