@extends('layouts.app', ['page' => 'orders-edit'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Orders'), __('Edit'), '# ' . $instance->id],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.edit-template class="orders-edit-form" action="{{ route('orders.update', $instance->id) }}">
        <div class="form__section">
            <x-forms.id-based-single-select.instance-edit-select
                label="Manufacturer"
                name="manufacturer_id"
                :options="$manufacturers"
                :instance="$instance"
                required />

            <x-forms.input.instance-edit-input
                label="PO №"
                name="purchase_order_name"
                :instance="$instance"
                :required="$instance->is_confirmed" />

            <x-forms.input.instance-edit-input
                type="date"
                label="PO date"
                name="purchase_order_date"
                :instance="$instance"
                :required="$instance->is_confirmed" />

            <x-forms.input.instance-edit-input
                type="date"
                label="Receive date"
                name="receive_date"
                :instance="$instance" />

            <x-forms.id-based-single-select.instance-edit-select
                label="Currency"
                name="currency_id"
                :options="$currencies"
                :instance="$instance"
                required />

            <x-forms.input.instance-edit-input
                type="date"
                label="Readiness date"
                name="readiness_date"
                :instance="$instance" />

            <x-forms.input.instance-edit-input
                type="date"
                label="Expected dispatch date"
                name="expected_dispatch_date"
                :instance="$instance" />
        </div>

        <x-different.button class="orders-edit-form__add-product-btn" style="success" icon="add">{{ __('Add new product') }}</x-different.button>
    </x-forms.template.edit-template>
@endsection
