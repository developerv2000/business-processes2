@extends('layouts.app', ['page' => 'invoice-items-edit'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Invoice items'), __('Edit'), '#' . $instance->id],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.edit-template action="{{ route('invoice-items.update', $instance->id) }}">
        <div class="form__section">
            <div class="form__row">
                <x-forms.input.instance-edit-input
                    label="Invoice"
                    name="readonly_invoice"
                    initialValue="{{ $instance->invoice->name }}"
                    :instance="$instance"
                    readonly
                    required />

                @if ($instance->isProductCategory())
                    <x-forms.input.instance-edit-input
                        label="Product"
                        name="readonly_product"
                        initialValue="{{ $instance->orderProduct->process->fixed_trademark_ru_for_order }}"
                        :instance="$instance"
                        readonly
                        required />
                @else
                    <x-forms.input.instance-edit-input
                        label="Name"
                        name="non_product_category_name"
                        :instance="$instance"
                        required />
                @endif
            </div>

            <div class="form__row">
                <x-forms.input.instance-edit-input
                    label="Quantity"
                    name="quantity"
                    :instance="$instance"
                    required />

                @if ($instance->isProductCategory())
                    <x-forms.input.instance-edit-input
                        label="Price"
                        name="readonly_product_price"
                        :instance="$instance"
                        initialValue="{{ $instance->orderProduct->invoice_price }}"
                        readonly />
                @else
                    <x-forms.input.instance-edit-input
                        label="Price"
                        type="number"
                        step="0.01"
                        name="non_product_category_price"
                        :instance="$instance"
                        required />
                @endif
            </div>

            @if ($instance->invoice->payment_date)
                <div class="form__row">
                    <x-forms.input.instance-edit-input
                        label="Paid"
                        type="number"
                        step="0.01"
                        name="amount_paid"
                        :instance="$instance"
                        required />

                    <div class="form-group"></div>
                </div>
            @endif
        </div>
    </x-forms.template.edit-template>
@endsection
