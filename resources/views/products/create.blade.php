@extends('layouts.app', ['page' => 'products-create'])

@section('main')
    <div class="pre-content pre-content--intended styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('IVP'), __('Create new')],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="create-form">{{ __('Store') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.create-template action="{{ route('products.store') }}">
        <div class="form__section">
            <x-forms.id-based-single-select.default-select
                label="Manufacturer"
                name="manufacturer_id"
                :options="$manufacturers"
                required />

            <x-forms.id-based-single-select.default-select
                label="Generic"
                name="inn_id"
                :options="$inns"
                required />

            <x-forms.id-based-single-select.default-select
                label="Form"
                name="form_id"
                :options="$productForms"
                required />
        </div>

        {{-- Empty container used to hold similar products after ajax request --}}
        <div class="form__section similar-records"></div>

        <div class="form__section">
            <x-forms.input.default-input
                label="Dosage"
                name="dosage" />

            <x-forms.input.default-input
                label="Pack"
                name="pack" />

            <x-forms.input.default-input
                label="Manufacturer Brand"
                name="brand" />

            <x-forms.id-based-single-select.default-select
                label="Product class"
                name="class_id"
                :options="$productClasses"
                required />
        </div>

        <div class="form__section">
            <x-forms.input.default-input
                label="MOQ"
                name="moq" />

            <x-forms.id-based-single-select.default-select
                label="Shelf life"
                name="shelf_life_id"
                :options="$shelfLifes"
                required />
        </div>

        <div class="form__section">
            <x-forms.input.default-input
                label="Dossier"
                name="dossier" />

            <x-forms.id-based-multiple-select.default-select
                label="Zones"
                name="zones[]"
                :options="$zones"
                required />

            <x-forms.input.default-input
                label="Bioequivalence"
                name="bioequivalence" />

            <x-forms.input.default-input
                label="Down payment"
                name="down_payment" />

            <x-forms.input.default-input
                label="Validity period"
                name="validity_period" />
        </div>

        <div class="form__section">
            <x-forms.radiogroup.default-radiogroup
                label="Registered in EU"
                name="registered_in_eu"
                :options="$booleanOptions"
                default-value="0"
                required />

            <x-forms.radiogroup.default-radiogroup
                label="Sold in EU"
                name="sold_in_eu"
                :options="$booleanOptions"
                default-value="0"
                required />
        </div>

        @include('comments.model-form-partials.create-form-fields')
    </x-forms.template.create-template>
@endsection
