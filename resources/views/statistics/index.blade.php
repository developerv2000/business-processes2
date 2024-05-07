@extends('layouts.app', ['page' => 'statistics-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('КПЭ отдела ОАП')],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])
        </div>

        {{-- @include('statistics.tables.current-status-table')
        @include('statistics.tables.status-periods-table') --}}
    </div>

@endsection

@section('rightbar')
    @include('filters.statistics')
@endsection
