@extends('filters.template')

@section('elements')
    <x-forms.input.request-based-input
        type="number"
        label="Year"
        name="year" />

    <x-forms.id-based-single-select.request-based-select
        label="Manufacturer"
        name="manufacturer_id"
        :options="$manufacturers" />

    <x-forms.id-based-single-select.request-based-select
        label="Analyst"
        name="analyst_user_id"
        :options="$analystUsers" />

    <x-forms.id-based-single-select.request-based-select
        label="BDM"
        name="bdm_user_id"
        :options="$bdmUsers" />

    <x-forms.id-based-single-select.request-based-select
        label="Country"
        name="country_id"
        :options="$countries" />

    <x-forms.input.request-based-input
        type="text"
        label="Who met"
        name="who_met" />

    @include('filters.partials.default-elements', [
        'includeIdInput' => true,
    ])
@endsection
