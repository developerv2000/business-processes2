@extends('layouts.app', ['page' => 'processes-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('Filtered records') . ' - ' . $records->total()],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])

            <div class="pre-content__actions">
                <x-different.linked-button style="action" icon="delete" href="{{ route('processes.trash') }}">{{ __('Trash') }}</x-different.linked-button>

                <x-different.button style="action" icon="view_column" data-click-action="show-modal" data-modal-selector=".edit-table-columns-modal">{{ __('Columns') }}</x-different.button>
                <x-different.button style="action" icon="remove" data-click-action="show-modal" data-modal-selector=".multiple-delete-modal">{{ __('Delete') }}</x-different.button>

                @unless ($request->user()->isTrainee())
                    <x-different.export-form action="{{ route('processes.export') }}" />
                    <x-different.products-selection-form model="Process" />
                @endunless
            </div>
        </div>

        @include('tables.default-template', ['tableName' => 'processes'])
    </div>

    <x-modals.multiple-delete action="{{ route('processes.destroy') }}" :force-delete="false" />
    <x-modals.edit-table-columns action="{{ route('settings.update-table-columns') }}" table="processes" :columns="$allTableColumns" />
@endsection

@section('rightbar')
    @include('filters.processes')
@endsection
