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
                    $contractInputName = $month['name'] . '_contract_plan';
                    $commentInputName = $month['name'] . '_comment';
                @endphp

                <x-forms.groups.default-group label="{{ __($month['name']) . ' Кк' }}" error-name="{{ $contractInputName }}" required="true">
                    <input
                        class="input"
                        type="number"
                        name="{{ $contractInputName }}"
                        value="{{ old($contractInputName, $instance->pivot->{$contractInputName}) }}"
                        required>
                </x-forms.groups.default-group>

                <x-forms.groups.default-group label="{{ __($month['name']) . ' ' . __('Comment') }}" error-name="{{ $commentInputName }}" required="0">
                    <textarea
                        class="textarea"
                        name="{{ $commentInputName }}"
                        rows="5">{{ old($commentInputName, $instance->pivot->{$commentInputName}) }}</textarea>
                </x-forms.groups.default-group>
            </div>
        @endforeach

    </x-forms.template.edit-template>
@endsection
