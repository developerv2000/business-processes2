@extends('filters.template')

@section('elements')
    <x-forms.date-range-input.request-based-input
        label="Status date"
        name="status_update_date" />

    @include('filters.partials.default-elements', [
        'includeIdInput' => true,
    ])
@endsection
