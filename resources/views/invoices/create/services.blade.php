@extends('layouts.app', ['page' => 'invoices-create-services'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Invoices'), __('Create new services')],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="create-form">{{ __('Store') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.create-template action="{{ route('invoices.store.services') }}">
        <input type="hidden" name="category_id" value="2"> {{-- Service category --}}
        <input type="hidden" name="payment_type_id" value="3"> {{-- Full payment --}}

        <div class="form__section">
            <div class="form__row">
                <x-forms.input.default-input
                    label="Invoice"
                    name="name"
                    required />

                <x-forms.input.default-input
                    label="Date"
                    type="datetime-local"
                    name="date"
                    required />

                <x-forms.id-based-single-select.default-select
                    label="Payer"
                    name="payer_id"
                    :options="$payers"
                    required />
            </div>

            <div class="form__row">
                <x-forms.id-based-single-select.default-select
                    label="Currency"
                    name="currency_id"
                    :options="$currencies"
                    :default-value="$defaultCurrency->id"
                    required />

                <x-forms.input.default-input
                    label="Payment refer"
                    name="group_name" />

                <div class="form-group"></div>
            </div>
        </div>

        {{-- Services list --}}
        <div class="invoices-create__services-list-wrapper styled-box">
            <h2 class="invoices-create__services-title main-title">Services</h2>

            <div class="invoices-create__services-list"></div>

            <x-different.button type="button" class="invoices-create__add-service-btn" style="action" icon="add" type="button">
                {{ __('Add service') }}
            </x-different.button>
        </div>
    </x-forms.template.create-template>
@endsection
