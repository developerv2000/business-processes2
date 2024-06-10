@extends('layouts.app', ['page' => 'processes-create'])

@section('main')
    <div class="pre-content pre-content--intended styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('VPS'), __('Create new')],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="create-form">{{ __('Store') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.create-template action="{{ route('processes.store') }}">
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        @include('processes.partials.about-product')

        <div class="form__section">
            <x-forms.id-based-single-select.default-select
                class="statuses-selectize selectize--manually-initializable"
                label="Product status"
                name="status_id"
                :options="$statuses"
                required />

            <x-forms.id-based-multiple-select.default-select
                class="country-codes-selectize selectize--manually-initializable"
                label="Search country"
                name="country_code_ids[]"
                :options="$countryCodes"
                required />

            <x-forms.boolean-select.default-select
                class="historical-process-selectize selectize--manually-initializable"
                label="Historical process"
                name="is_historical"
                required />
        </div>

        <div class="form__section historical-process-date-container">
            <x-forms.input.default-input
                type="date"
                label="Historical process date"
                name="historical_date" />
        </div>

        <div class="processes-create__forecast-inputs-container form">@include('processes.partials.create-form-forecast-inputs', ['stage' => 1, 'selectedCountryCodes' => null])</div>

        <div class="processes-create__stage-inputs-container form">
            @include('processes.partials.create-form-stage-inputs', ['stage' => 1, 'product' => $product])
        </div>

        @include('comments.model-form-partials.create-form-fields')
    </x-forms.template.create-template>
@endsection
