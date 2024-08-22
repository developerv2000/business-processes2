@extends('layouts.app', ['page' => 'plan-mah-create'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [
                __('SPG'),
                $plan->year,
                '<a href="' . route('plan.country.codes.index', $plan->id) . '">' . __('Countries') . '</a>',
                $countryCode->name,
                '<a href="' . route('plan.marketing.authorization.holders.index', ['plan' => $plan->id, 'countryCode' => $countryCode->id]) . '">' . __('MAH') . '</a>',
                __('Create new'),
            ],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="create-form">{{ __('Store') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.create-template action="{{ route('plan.marketing.authorization.holders.store', ['plan' => $plan->id, 'countryCode' => $countryCode->id]) }}">
        <div class="form__section">
            <x-forms.id-based-single-select.default-select
                label="MAH"
                name="marketing_authorization_holder_id"
                :options="$marketingAuthorizationHolders"
                required />
        </div>

        @foreach ($calendarMonths as $month)
            <div class="form__section">
                <x-forms.input.default-input
                    :label="__($month['name']) . ' Кк'"
                    :name="$month['name'] . '_contract_plan'"
                    type="number" />

                <x-forms.input.default-input
                    :label="__($month['name']) . ' НПР'"
                    :name="$month['name'] . '_register_plan'"
                    type="number" />

                <x-forms.textarea.default-textarea
                    :label="__($month['name']) . ' ' . __('Comment')"
                    :name="$month['name'] . '_comment'"
                    rows="5" />
            </div>
        @endforeach

    </x-forms.template.create-template>
@endsection