@extends('layouts.app', ['page' => 'plan-mah-edit'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [
                __('SPG'),
                $plan->year,
                '<a href="' . route('plan.country.codes.index', $plan->id) . '">' . __('Countries') . '</a>',
                $countryCode->name,
                '<a href="' . route('plan.marketing.authorization.holders.index', ['plan' => $plan->id, 'countryCode' => $countryCode->id]) . '">' . __('MAH') . '</a>',
                $instance->name,
                __('Edit'),
            ],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.edit-template
        action="{{ route('plan.marketing.authorization.holders.update', ['plan' => $plan->id, 'countryCode' => $countryCode->id, 'marketingAuthorizationHolder' => $instance->id]) }}">
        <div class="form__section">
            <x-forms.input.default-input
                label="Name"
                :value="$instance->name"
                name="readonly"
                readonly />
        </div>

        @foreach ($calendarMonths as $month)
            <div class="form__section">
                @php
                    $europeContractInputName = $month['name'] . '_europe_contract_plan';
                    $indiaContractInputName = $month['name'] . '_india_contract_plan';
                    $commentInputName = $month['name'] . '_comment';
                @endphp

                <h2 class="main-title">{{ __($month['name']) }}</h2>

                <x-forms.groups.default-group label="EU Кк" error-name="{{ $europeContractInputName }}" required="true">
                    <input
                        class="input"
                        type="number"
                        name="{{ $europeContractInputName }}"
                        value="{{ old($europeContractInputName, $instance->pivot->{$europeContractInputName}) }}"
                        required>
                </x-forms.groups.default-group>

                <x-forms.groups.default-group label="EU Кк" error-name="{{ $indiaContractInputName }}" required="true">
                    <input
                        class="input"
                        type="number"
                        name="{{ $indiaContractInputName }}"
                        value="{{ old($indiaContractInputName, $instance->pivot->{$indiaContractInputName}) }}"
                        required>
                </x-forms.groups.default-group>

                <x-forms.groups.default-group label="{{ __('Comment') }}" error-name="{{ $commentInputName }}" required="0">
                    <textarea
                        class="textarea"
                        name="{{ $commentInputName }}"
                        rows="5">{{ old($commentInputName, $instance->pivot->{$commentInputName}) }}</textarea>
                </x-forms.groups.default-group>
            </div>
        @endforeach

    </x-forms.template.edit-template>
@endsection
