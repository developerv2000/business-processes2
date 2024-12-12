@extends('layouts.app', ['page' => 'orders-edit'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Orders'), __('Edit'), $instance->purchase_order_name ?: '# ' . $instance->id],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.edit-template action="{{ route('orders.update', $instance->id) }}">
        <div class="form__section">
            <x-forms.id-based-single-select.instance-edit-select
                label="Manufacturer"
                name="manufacturer_id"
                :options="$manufacturers"
                :instance="$instance"
                required />

            <x-forms.id-based-single-select.instance-edit-select
                label="Country"
                name="country_code_id"
                :options="$countryCodes"
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
                :initial-value="$instance->purchase_order_date?->isoFormat('YYYY-MM-DD')"
                :required="$instance->is_confirmed" />

            <x-forms.input.instance-edit-input
                type="date"
                label="Receive date"
                name="receive_date"
                :initial-value="$instance->receive_date?->isoFormat('YYYY-MM-DD')"
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
                :initial-value="$instance->readiness_date?->isoFormat('YYYY-MM-DD')"
                :instance="$instance" />

            <x-forms.input.instance-edit-input
                type="date"
                label="Expected dispatch date"
                name="expected_dispatch_date"
                :initial-value="$instance->expected_dispatch_date?->isoFormat('YYYY-MM-DD')"
                :instance="$instance" />

            <x-forms.input.default-input
                label="File"
                type="file"
                name="file" />
        </div>

        @include('comments.model-form-partials.edit-form-fields')
    </x-forms.template.edit-template>
@endsection
