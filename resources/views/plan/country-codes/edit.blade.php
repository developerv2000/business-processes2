@extends('layouts.app', ['page' => 'plan-country-codes-edit'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('SPG'), $plan->year, '<a href="' . route('plan.country.codes.index', $plan->id) . '">' . __('Countries') . '</a>', $instance->name, __('Edit')],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.edit-template action="{{ route('plan.country.codes.update', ['plan' => $plan->id, 'countryCode' => $instance->id]) }}">
        <div class="form__section">
            <x-forms.input.default-input
                label="Country"
                :value="$instance->name"
                name="readonly"
                readonly />

            <x-forms.groups.default-group label="{{ __('Comment') }}" error-name="comment">
                <textarea name="comment" class="textarea" rows="5">{{ old('comment', $instance->pivot->comment) }}</textarea>
            </x-forms.groups.default-group>
        </div>

    </x-forms.template.edit-template>
@endsection
