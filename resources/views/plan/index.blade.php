@extends('layouts.app', ['page' => 'plan-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('Filtered records') . ' - ' . count($records)],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])

            <div class="pre-content__actions">
                <x-different.linked-button style="action" icon="add" href="{{ route('plan.create') }}">{{ __('New') }}</x-different.linked-button>
                <x-different.button style="action" icon="remove" data-click-action="show-modal" data-modal-selector=".multiple-delete-modal">{{ __('Delete') }}</x-different.button>
            </div>
        </div>

        @include('plan.partials.index-table')
    </div>

    <x-modals.multiple-delete action="{{ route('plan.destroy') }}" :force-delete="false" />
@endsection
