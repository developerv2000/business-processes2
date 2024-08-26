@extends('filters.template')

@section('elements')
    <x-forms.input.request-based-input
        type="number"
        label="Year"
        name="year" />

    <x-forms.single-select.request-based-select
        label="Manufacturer countries"
        name="specific_manufacturer_country"
        :options="$specificManufacturerCountries" />
@endsection
