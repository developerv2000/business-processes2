@extends('layouts.app', ['page' => 'processes-trash'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('Trash'), __('Filtered records') . ' - ' . $records->total()],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])

            <div class="pre-content__actions">
                <x-different.button style="action" icon="view_column" data-click-action="show-modal" data-modal-selector=".edit-table-columns-modal">{{ __('Columns') }}</x-different.button>

                @can('edit-vps')
                    <x-different.button style="action" icon="history" data-click-action="show-modal" data-modal-selector=".multiple-restore-modal">{{ __('Restore') }}</x-different.button>
                @endcan

                @can('delete-from-trash')
                    <x-different.button style="action" icon="remove" data-click-action="show-modal" data-modal-selector=".multiple-delete-modal">{{ __('Permanent delete') }}</x-different.button>
                @endcan
            </div>
        </div>

        @include('tables.trash-template', ['tableName' => 'processes'])
    </div>

    @can('delete-from-trash')
        <x-modals.multiple-delete action="{{ route('processes.destroy') }}" :force-delete="true" />
    @endcan

    @can('edit-vps')
        <x-modals.multiple-restore action="{{ route('processes.restore') }}" />
    @endcan

    <x-modals.edit-table-columns action="{{ route('settings.update-table-columns') }}" table="processes" :columns="$allTableColumns" />
@endsection

@section('rightbar')
    @include('filters.processes')
@endsection
