@extends('layouts.app', ['page' => 'invoice-items-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('Filtered records') . ' - ' . $records->count()],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])
        </div>

        @include('invoice-items.partials.table')
    </div>
@endsection
