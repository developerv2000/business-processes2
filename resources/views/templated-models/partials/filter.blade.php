@extends('filters.template')

@section('elements')
    @if ($modelAttributes->contains('name'))
        <x-forms.id-based-single-select.request-based-select
            label="Name"
            name="id"
            :options="$allRecords" />
    @endif

    @if ($modelAttributes->contains('parent_id'))
        <x-forms.id-based-single-select.request-based-select
            label="Parent"
            name="parent_id"
            :options="$parentRecords" />
    @endif
@endsection
