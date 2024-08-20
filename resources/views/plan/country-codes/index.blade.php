@extends('layouts.app', ['page' => 'plan-country-code-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('SPG'), $plan->year, __('Edit'), __('Countries')],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])

            <div class="pre-content__actions">
                <x-different.linked-button style="action" icon="add" href="{{ route('plan.country.codes.create', $plan->id) }}">{{ __('New') }}</x-different.linked-button>

                <x-different.button style="action" icon="remove" data-click-action="show-modal" data-modal-selector=".multiple-delete-modal">{{ __('Delete') }}</x-different.button>
            </div>
        </div>

        @include('plan.country-codes.partials.table')
    </div>

    <x-modals.multiple-delete action="{{ route('plan.country.codes.destroy', $plan->id) }}" :force-delete="false" />
@endsection
