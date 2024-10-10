@extends('layouts.app', ['page' => 'kvpp-trash'])

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

                @can('edit-kvpp')
                    <x-different.button style="action" icon="history" data-click-action="show-modal" data-modal-selector=".multiple-restore-modal">{{ __('Restore') }}</x-different.button>
                @endcan

                @can('delete-from-trash')
                    <x-different.button style="action" icon="remove" data-click-action="show-modal" data-modal-selector=".multiple-delete-modal">{{ __('Permanent delete') }}</x-different.button>
                @endcan
            </div>
        </div>

        @include('tables.trash-template', ['tableName' => 'kvpp'])
    </div>

    @can('delete-from-trash')
        <x-modals.multiple-delete action="{{ route('kvpp.destroy') }}" :force-delete="true" />
    @endcan

    @can('edit-kvpp')
        <x-modals.multiple-restore action="{{ route('kvpp.restore') }}" />
    @endcan

    <x-modals.edit-table-columns action="{{ route('settings.update-table-columns') }}" table="kvpp" :columns="$allTableColumns" />
@endsection

@section('rightbar')
    @include('filters.kvpp')
@endsection
