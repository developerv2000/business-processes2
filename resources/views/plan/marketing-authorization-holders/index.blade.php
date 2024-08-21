@extends('layouts.app', ['page' => 'plan-mah-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('SPG'), $plan->year, '<a href="' . route('plan.country.codes.index', $plan->id) . '">' . __('Countries') . '</a>', $countryCode->name, __('MAH')],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])

            <div class="pre-content__actions">
                <x-different.linked-button style="action" icon="add"
                    href="{{ route('plan.marketing.authorization.holders.create', ['plan' => $plan->id, 'countryCode' => $countryCode->id]) }}">{{ __('New') }}</x-different.linked-button>

                <x-different.button style="action" icon="remove" data-click-action="show-modal" data-modal-selector=".multiple-delete-modal">{{ __('Delete') }}</x-different.button>
            </div>
        </div>

        @include('plan.marketing-authorization-holders.partials.table')
    </div>

    <x-modals.multiple-delete action="{{ route('plan.marketing.authorization.holders.destroy', ['plan' => $plan->id, 'countryCode' => $countryCode->id]) }}" :force-delete="false" />
@endsection
