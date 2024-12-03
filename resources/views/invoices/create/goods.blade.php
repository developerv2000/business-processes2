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

    <x-forms.template.create-template action="{{ route('invoices.store.goods') }}">
        <div class="form__section">
            <div class="form__row">
                <x-forms.id-based-single-select.default-select
                    label="Order"
                    name="order_id"
                    :options="$orders"
                    optionCaptionAttribute="label"
                    required />

                <x-forms.input.default-input
                    label="Manufacturer"
                    name="name"
                    required />
            </div>
        </div>
    </x-forms.template.create-template>
@endsection
