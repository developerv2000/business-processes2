@extends('layouts.app', ['page' => 'plan-show'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('SPG') . ' - ' . $request->year],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])
        </div>

        @include('plan.partials.show-table')
    </div>
@endsection

@section('rightbar')
    @include('filters.plan')
@endsection
