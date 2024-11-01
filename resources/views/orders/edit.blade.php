@extends('layouts.app', ['page' => 'orders-edit'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Orders'), __('Edit'), '# ' . $instance->id],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Save changes') }}</x-different.button>
        </div>
    </div>

    <form class="form edit-form orders-edit-form" action="{{ route('orders.update', $instance->id) }}" id="edit-form" method="POST" data-on-submit="show-spinner">
        @csrf
        @method('PATCH')
        <input type="hidden" name="previous_url" value="{{ old('previous_url', url()->previous()) }}">
        <input type="hidden" name="order_id" value="{{ $instance->id }}">

        {{-- Wrapper for attaching create product inputs section --}}
        <div class="orders-edit-form__sections-wrapper">
            <div class="form__section">
                <h1 class="main-title">{{ __('Order') }}</h1>

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

            @foreach ($instance->products as $product)
                @include('orders.partials.products-edit-section', ['product' => $product])
            @endforeach
        </div>

        <div class="orders-edit__form-buttons-wrapper">
            <x-different.button type="button" class="orders-edit-form__add-product-btn" style="success" icon="add">
                {{ __('Add new product') }}
            </x-different.button>

            <x-different.button class="form__submit" type="submit" icon="done_all">{{ __('Save changes') }}</x-different.button>
        </div>
    </form>
@endsection
