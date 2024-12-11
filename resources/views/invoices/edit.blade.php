@extends('layouts.app', ['page' => 'invoices-edit'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Invoices'), __('Edit'), $instance->name],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.edit-template action="{{ route('invoices.update', $instance->id) }}">
        <div class="form__section">
            <div class="form__row">
                <x-forms.input.instance-edit-input
                    label="Invoice"
                    name="name"
                    :instance="$instance"
                    required />

                <x-forms.input.instance-edit-input
                    label="Date"
                    name="date"
                    type="datetime-local"
                    :instance="$instance"
                    required />

                <x-forms.id-based-single-select.instance-edit-select
                    label="Payer"
                    name="payer_id"
                    :options="$payers"
                    :instance="$instance"
                    required />
            </div>

            <div class="form__row">
                <x-forms.input.instance-edit-input
                    label="Payment type"
                    name="readonly_payment_type"
                    initialValue="{{ $instance->paymentType->name }}"
                    :instance="$instance"
                    readonly
                    required />

                <x-forms.id-based-single-select.instance-edit-select
                    label="Curency"
                    name="currency_id"
                    :options="$currencies"
                    :instance="$instance"
                    required />

                <x-forms.input.instance-edit-input
                    label="Payment refer"
                    name="group_name"
                    :instance="$instance" />
            </div>

            @if ($instance->isGoodsCategory())
                <div class="form__row">
                    <x-forms.input.instance-edit-input
                        label="Orders"
                        name="readonly_orders"
                        initialValue="{{ $instance->orders->pluck('purchase_order_name')->join(' ') }}"
                        :instance="$instance"
                        readonly />

                    <x-forms.input.instance-edit-input
                        label="Terms"
                        name="terms"
                        :instance="$instance"
                        readonly />

                    <div class="form-group"></div>
                </div>
            @endif
        </div>

        <div class="form__section">
            <div class="form__row">
                <x-forms.input.instance-edit-input
                    label="Send to pay"
                    name="sent_for_payment_date"
                    type="datetime-local"
                    :instance="$instance" />

                <x-forms.input.instance-edit-input
                    label="Pay date"
                    name="payment_date"
                    type="datetime-local"
                    :instance="$instance" />

                <x-forms.boolean-select.instance-edit-select
                    label="Cancelled"
                    name="cancelled"
                    :instance="$instance"
                    required />
            </div>
        </div>
    </x-forms.template.edit-template>
@endsection
