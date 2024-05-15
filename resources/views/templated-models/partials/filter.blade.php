@extends('filters.template')

@section('elements')
    <x-forms.id-based-single-select.request-based-select
        label="Name"
        name="id"
        :options="$allRecords" />
@endsection
