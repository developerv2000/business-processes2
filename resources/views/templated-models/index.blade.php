@extends('layouts.app', ['page' => 'templated-models-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('Different')],
                'fullScreen' => false,
            ])
        </div>

        @include('templated-models.partials.index-page-table')
    </div>
@endsection
