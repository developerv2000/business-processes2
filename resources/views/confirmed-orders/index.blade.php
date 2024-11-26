@extends('layouts.app', ['page' => 'confirmed-orders-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('Filtered records') . ' - ' . $records->total()],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])
        </div>

        @include('tables.default-template', ['tableName' => 'confirmed-orders'])
    </div>
@endsection
