@extends('layouts.app', ['page' => 'plan-country-codes-create'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('SPG'), $plan->year, '<a href="' . route('plan.country.codes.index', $plan->id) . '">' . __('Countries') . '</a>', __('Create new')],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="create-form">{{ __('Store') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.create-template action="{{ route('plan.country.codes.store', $plan->id) }}">
        <div class="form__section">
            <x-forms.id-based-single-select.default-select
                label="Country"
                name="country_code_id"
                :options="$countryCodes"
                required />

            <x-forms.id-based-multiple-select.default-select
                label="MAH"
                name="marketing_authorization_holder_ids[]"
                :options="$marketingAuthorizationHolders" />
        </div>

    </x-forms.template.create-template>
@endsection
