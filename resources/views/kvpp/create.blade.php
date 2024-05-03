@extends('layouts.app', ['page' => 'kvpp-create'])

@section('main')
    <div class="pre-content pre-content--intended styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('KVPP'), __('Create new')],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="create-form">{{ __('Store') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.create-template action="{{ route('kvpp.store') }}">


        @include('comments.model-form-partials.create-form-fields')
    </x-forms.template.create-template>
@endsection
