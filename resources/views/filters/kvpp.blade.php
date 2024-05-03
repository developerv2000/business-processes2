@extends('filters.template')

@section('elements')
    

    @include('filters.partials.default-elements', [
        'includeIdInput' => true,
    ])
@endsection
