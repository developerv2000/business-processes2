@extends('filters.template')

@section('elements')
    <x-forms.id-based-single-select.request-based-select
        name="analyst_user_id"
        label="Analyst"
        :options="$analystUsers"
        option-caption-attribute="name"
    />
@endsection

