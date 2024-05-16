@extends('layouts.app', ['page' => 'templated-models-edit'])

@section('main')
    <div class="pre-content pre-content--intended styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [
                '<a href="' . route('templated-models.show', $model['name'])  . '">' . $model['name'] . '</a>',
                __('Edit'),
                $modelAttributes->contains('name') ? $instance->name : $instance->id
            ],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    {{-- Personal data --}}
    <x-forms.template.edit-template action="{{ route('templated-models.update', ['modelName' => $model['name'], 'id' => $instance->id]) }}">
        <div class="form__section">
            @if ($modelAttributes->contains('name'))
                <x-forms.input.instance-edit-input
                    label="Name"
                    name="name"
                    :instance="$instance"
                    required />
            @endif

            @if ($modelAttributes->contains('parent_id'))
                <x-forms.id-based-single-select.instance-edit-select
                    label="Parent"
                    name="parent_id"
                    :instance="$instance"
                    :options="$parentRecords" />
            @endif
        </div>
    </x-forms.template.edit-template>
@endsection
