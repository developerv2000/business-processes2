@extends('layouts.app', ['page' => 'invoices-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('Filtered records') . ' - ' . $records->count()],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])
        </div>

        @include('invoices.partials.table')
    </div>
@endsection
