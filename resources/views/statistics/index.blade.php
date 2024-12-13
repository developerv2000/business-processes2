@extends('layouts.app', ['page' => 'statistics-index'])

@section('main')
    <div class="main__conent-box">
        @include('statistics.partials.counter')
        @include('statistics.tables.current-statusses-table')
        @include('statistics.charts.processes-count-chart')
        @include('statistics.tables.maximum-statusses-table')
        @include('statistics.charts.maximum-processes-count-chart')

        @include('statistics.tables.active-manufacturers-table')
        @include('statistics.charts.active-manufacturers-chart')
    </div>
@endsection

@section('rightbar')
    @include('filters.statistics')
@endsection
