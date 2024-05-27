@extends('layouts.app', ['page' => 'comments-edit'])

@section('main')
    <div class="pre-content pre-content--intended styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [$title, __('Comments') . ' # ' . $instance->id],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.edit-template action="{{ route('comments.update', $instance->id) }}">
        <div class="form__section">
            <x-forms.textarea.instance-edit-textarea
                label="Comment"
                name="body"
                :instance="$instance"
                required />

            <x-forms.input.instance-edit-input
                label="Date of creation"
                name="created_at"
                :instance="$instance"
                required />
        </div>
    </x-forms.template.edit-template>
@endsection
