@extends('layouts.app', ['page' => 'plan-show'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('SPG') . ' - ' . $plan->year],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])

            <div class="pre-content__actions">
                <form action="{{ route('plan.export') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <input type="hidden" name="specific_manufacturer_country" value="{{ $request->specific_manufacturer_country }}">

                    <x-different.button style="action" icon="download" type="submit">{{ __('Export') }}</x-different.button>
                </form>
            </div>
        </div>

        @include('plan.partials.show-table')
    </div>
@endsection

@section('rightbar')
    @include('filters.plan')
@endsection
