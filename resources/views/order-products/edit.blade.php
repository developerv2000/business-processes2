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
                label="Brand name ENG"
                name="process_id"
                :options="$processes"
                optionCaptionAttribute="fixed_trademark_en_for_order"
                :instance="$product"
                required />

            <x-forms.id-based-single-select.instance-edit-select
                label="Country"
                name="country_code_id"
                :options="$countryCodes"
                :instance="$product"
                required />

            <x-forms.id-based-single-select.instance-edit-select
                label="MAH"
                name="marketing_authorization_holder_id"
                :options="$marketingAuthorizationHolders"
                :instance="$product"
                required />

            <x-forms.input.instance-edit-input
                label="Quantity"
                name="quantity"
                :instance="$product"
                type="number" />

            <x-forms.input.instance-edit-input
                type="number"
                step="0.01"
                label="Price"
                name="price"
                :instance="$product"
                required />
        </div>

        @include('comments.model-form-partials.edit-form-fields')
    </x-forms.template.edit-template>
@endsection
