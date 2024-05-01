@extends('layouts.app', ['page' => 'processes-edit'])

@section('main')
    <div class="pre-content pre-content--intended styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('VPS'), __('Edit'), '# ' . $instance->id],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.edit-template action="{{ route('processes.update', $instance->id) }}">


        @include('comments.model-form-partials.edit-form-fields')
    </x-forms.template.edit-template>
@endsection
