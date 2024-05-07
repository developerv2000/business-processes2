@extends('filters.template')

@section('elements')
    <x-forms.id-based-single-select.request-based-select
        label="Analyst"
        name="analyst_user_id"
        :options="$analystUsers" />

    <x-forms.id-based-single-select.request-based-select
        label="BDM"
        name="bdm_user_id"
        :options="$bdmUsers" />
@endsection
