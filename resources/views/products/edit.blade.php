@extends('layouts.app', ['page' => 'products-edit'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('IVP'), __('Edit'), '# ' . $instance->id],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.edit-template action="{{ route('products.update', $instance->id) }}">
        <div class="form__section">
            <x-forms.id-based-single-select.instance-edit-select
                label="Manufacturer"
                name="manufacturer_id"
                :options="$manufacturers"
                :instance="$instance"
                required />

            <x-forms.id-based-single-select.instance-edit-select
                label="Generic"
                name="inn_id"
                :options="$inns"
                :instance="$instance"
                required />

            <x-forms.id-based-single-select.instance-edit-select
                label="Form"
                name="form_id"
                :options="$productForms"
                :instance="$instance"
                required />
        </div>

        <div class="form__section">
            <x-forms.input.instance-edit-input
                label="Dosage"
                name="dosage"
                :instance="$instance"
                data-on-input="validate-specific-input" />

            <x-forms.input.instance-edit-input
                label="Pack"
                name="pack"
                :instance="$instance"
                data-on-input="validate-specific-input" />

            <x-forms.input.instance-edit-input
                label="Manufacturer Brand"
                name="brand"
                :instance="$instance" />

            <x-forms.id-based-single-select.instance-edit-select
                label="Product class"
                name="class_id"
                :options="$productClasses"
                :instance="$instance"
                required />
        </div>

        <div class="form__section">
            <x-forms.input.instance-edit-input
                label="MOQ"
                name="moq"
                :instance="$instance" />

            <x-forms.id-based-single-select.instance-edit-select
                label="Shelf life"
                name="shelf_life_id"
                :options="$shelfLifes"
                :instance="$instance"
                required />
        </div>

        <div class="form__section">
            <x-forms.input.instance-edit-input
                label="Dossier"
                name="dossier"
                :instance="$instance" />

            <x-forms.id-based-multiple-select.instance-edit-select
                label="Zones"
                name="zones[]"
                :options="$zones"
                :instance="$instance"
                required />

            <x-forms.input.instance-edit-input
                label="Bioequivalence"
                name="bioequivalence"
                :instance="$instance" />

            <x-forms.input.instance-edit-input
                label="Down payment"
                name="down_payment"
                :instance="$instance" />

            <x-forms.input.instance-edit-input
                label="Validity period"
                name="validity_period"
                :instance="$instance" />
        </div>

        <div class="form__section">
            <x-forms.radiogroup.instance-edit-radiogroup
                label="Registered in EU"
                name="registered_in_eu"
                :options="$booleanOptions"
                :instance="$instance"
                required />

            <x-forms.radiogroup.instance-edit-radiogroup
                label="Sold in EU"
                name="sold_in_eu"
                :options="$booleanOptions"
                :instance="$instance"
                required />
        </div>

        @include('comments.model-form-partials.edit-form-fields')
        @include('attachments.partials.form-add-input')
    </x-forms.template.edit-template>
@endsection
