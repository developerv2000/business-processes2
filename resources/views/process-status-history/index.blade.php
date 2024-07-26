@extends('layouts.app', ['page' => 'process-status-history-index'])

@section('main')
    @include('process-status-history.partials.about-process')

    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('History') . ' - ' . $records->count()],
                'fullScreen' => false,
            ])
        </div>

        @include('process-status-history.partials.index-table')
    </div>

@endsection
