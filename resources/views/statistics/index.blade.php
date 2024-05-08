@extends('layouts.app', ['page' => 'statistics-index'])

@section('main')
    <div class="main__conent-box">
        @include('statistics.partials.counter')
        @include('statistics.tables.current-statusses-table')
        @include('statistics.tables.transitional-statusses-table')
    </div>
@endsection

@section('rightbar')
    @include('filters.statistics')
@endsection
