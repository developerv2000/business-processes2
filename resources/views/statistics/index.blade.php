@extends('layouts.app', ['page' => 'statistics-index'])

@section('main')
    <div class="main__conent-box">
        @include('statistics.partials.counter')
        @include('statistics.partials.charts')
        @include('statistics.tables.current-statusses-table')
        @include('statistics.tables.maximum-statusses-table')
    </div>
@endsection

@section('rightbar')
    @include('filters.statistics')
@endsection
