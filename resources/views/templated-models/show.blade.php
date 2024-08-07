@extends('layouts.app', ['page' => 'templated-models-show'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [$model['name'], __('Filtered records') . ' - ' . $model['items_count']],
                'fullScreen' => false,
            ])

            <div class="pre-content__actions">
                <x-different.linked-button style="action" icon="add" href="{{ route('templated-models.create', $model['name']) }}">{{ __('New') }}</x-different.linked-button>
                <x-different.button style="action" icon="remove" data-click-action="show-modal" data-modal-selector=".multiple-delete-modal">{{ __('Delete') }}</x-different.button>
            </div>
        </div>

        <x-errors.single name="templated_models_deletion" />

        @include('templated-models.partials.show-page-table')
    </div>

    <x-modals.multiple-delete action="{{ route('templated-models.destroy', $model['name']) }}" :force-delete="false" />
@endsection

@section('rightbar')
    @include('templated-models.partials.filter')
@endsection
