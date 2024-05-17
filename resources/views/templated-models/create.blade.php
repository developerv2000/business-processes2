@extends('layouts.app', ['page' => 'templated-models-create'])

@section('main')
    <div class="pre-content pre-content--intended styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [
                '<a href="' . route('templated-models.show', $model['name'])  . '">' . $model['name'] . '</a>'
                , __('Create new')
            ],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="create-form">{{ __('Store') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.create-template action="{{ route('templated-models.store', $model['name']) }}">
        <div class="form__section">
            @if ($modelAttributes->contains('name'))
                <x-forms.input.default-input
                    label="Name"
                    name="name"
                    data-on-input="{{ $model['name'] == 'Inn' ? 'validate-specific-input' : '' }}"
                    required />
            @endif

            @if ($modelAttributes->contains('parent_id'))
                <x-forms.id-based-single-select.default-select
                    label="Parent"
                    name="parent_id"
                    :options="$parentRecords" />
            @endif
        </div>
    </x-forms.template.create-template>
@endsection
