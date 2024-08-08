@extends('layouts.app', ['page' => 'manufacturers-edit'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('EPP'), __('Edit'), $instance->name],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.edit-template action="{{ route('manufacturers.update', $instance->id) }}">
        <div class="form__section">
            <x-forms.input.instance-edit-input
                label="Manufacturer"
                name="name"
                :instance="$instance"
                required />

            <x-forms.id-based-single-select.instance-edit-select
                label="Category"
                name="category_id"
                :options="$categories"
                :instance="$instance"
                required />

            <x-forms.id-based-single-select.instance-edit-select
                label="BDM"
                name="bdm_user_id"
                :options="$bdmUsers"
                :instance="$instance"
                required />

            <x-forms.id-based-single-select.instance-edit-select
                label="Analyst"
                name="analyst_user_id"
                :options="$analystUsers"
                :instance="$instance"
                required />

            <x-forms.id-based-single-select.instance-edit-select
                label="Country"
                name="country_id"
                :options="$countries"
                :instance="$instance"
                required />
        </div>

        <div class="form__section">
            <x-forms.input.instance-edit-input
                label="Website"
                name="website"
                :instance="$instance" />

            <x-forms.textarea.instance-edit-textarea
                label="About company"
                name="about"
                :instance="$instance" />

            <x-forms.input.instance-edit-input
                label="Relationship"
                name="relationship"
                :instance="$instance" />
        </div>

        <div class="form__section">
            <x-forms.radiogroup.instance-edit-radiogroup
                label="Status"
                name="is_active"
                :options="$statusOptions"
                :instance="$instance"
                required />

            <x-forms.radiogroup.instance-edit-radiogroup
                label="Important"
                name="is_important"
                :options="$booleanOptions"
                :instance="$instance"
                required />
        </div>

        <div class="form__section">
            <x-forms.multiple-select.instance-edit-select
                label="Presence"
                name="presences[]"
                :taggable="true"
                :options="$instance->presences->pluck('name')"
                :instance="$instance" />

            <x-forms.id-based-multiple-select.instance-edit-select
                label="Zones"
                name="zones[]"
                :options="$zones"
                :instance="$instance"
                required />

            <x-forms.id-based-multiple-select.instance-edit-select
                label="Product category"
                name="productClasses[]"
                :options="$productClasses"
                :instance="$instance"
                required />

            <x-forms.id-based-multiple-select.instance-edit-select
                label="Black list"
                name="blacklists[]"
                :instance="$instance"
                :options="$blacklists" />
        </div>

        @include('comments.model-form-partials.edit-form-fields')
        @include('attachments.partials.form-add-input')
    </x-forms.template.edit-template>
@endsection
