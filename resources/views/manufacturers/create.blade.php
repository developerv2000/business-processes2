@extends('layouts.app', ['page' => 'manufacturers-create'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('EPP'), __('Create new')],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="create-form">{{ __('Store') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.create-template action="{{ route('manufacturers.store') }}">
        <div class="form__section">
            <x-forms.input.default-input
                label="Manufacturer"
                name="name"
                required />

            <x-forms.id-based-single-select.default-select
                label="Category"
                name="category_id"
                :options="$categories"
                required />

            <x-forms.id-based-single-select.default-select
                label="BDM"
                name="bdm_user_id"
                :options="$bdmUsers"
                required />

            <x-forms.id-based-single-select.current-user-select
                label="Analyst"
                name="analyst_user_id"
                :options="$analystUsers"
                required />

            <x-forms.id-based-single-select.default-select
                label="Country"
                name="country_id"
                :options="$countries"
                required />
        </div>

        <div class="form__section">
            <x-forms.input.default-input
                label="Website"
                name="website" />

            <x-forms.textarea.default-textarea
                label="About company"
                name="about" />

            <x-forms.input.default-input
                label="Relationship"
                name="relationship" />
        </div>

        <div class="form__section">
            <x-forms.radiogroup.default-radiogroup
                label="Status"
                name="is_active"
                :options="$statusOptions"
                default-value="0"
                required />

            <x-forms.radiogroup.default-radiogroup
                label="Important"
                name="is_important"
                :options="$booleanOptions"
                default-value="0"
                required />
        </div>

        <div class="form__section">
            <x-forms.multiple-select.default-select
                label="Presence"
                name="presences[]"
                :taggable="true"
                :options="[]" />

            <x-forms.id-based-multiple-select.default-select
                label="Zones"
                name="zones[]"
                :options="$zones"
                required />

            <x-forms.id-based-multiple-select.default-select
                label="Product category"
                name="productClasses[]"
                :options="$productClasses"
                required />

            <x-forms.id-based-multiple-select.default-select
                label="Black list"
                name="blacklists[]"
                :options="$blacklists" />
        </div>

        @include('comments.model-form-partials.create-form-fields')
        @include('attachments.partials.form-add-input')
    </x-forms.template.create-template>
@endsection
