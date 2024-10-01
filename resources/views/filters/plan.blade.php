@extends('filters.template')

@section('elements')
    <x-forms.single-select.request-based-select
        label="Plan for"
        name="specific_manufacturer_country"
        :options="$specificManufacturerCountries" />
@endsection
