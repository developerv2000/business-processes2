@extends('layouts.app', ['page' => 'applications-edit'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Applications'), __('Edit'), '# ' . $instance->id],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.edit-template action="{{ route('applications.update', $instance->id) }}">
        <div class="form__section">
            <x-forms.input.instance-edit-input
                label="PO №"
                name="name"
                :instance="$instance"
                required />
        </div>
    </x-forms.template.edit-template>
@endsection
