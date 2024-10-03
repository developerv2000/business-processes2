@extends('layouts.app', ['page' => 'manufacturers-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('Filtered records') . ' - ' . $records->total()],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])

            <div class="pre-content__actions">
                <x-different.linked-button style="action" icon="add" href="{{ route('manufacturers.create') }}">{{ __('New') }}</x-different.linked-button>
                <x-different.linked-button style="action" icon="delete" href="{{ route('manufacturers.trash') }}">{{ __('Trash') }}</x-different.linked-button>

                <x-different.button style="action" icon="view_column" data-click-action="show-modal" data-modal-selector=".edit-table-columns-modal">{{ __('Columns') }}</x-different.button>
                <x-different.button style="action" icon="remove" data-click-action="show-modal" data-modal-selector=".multiple-delete-modal">{{ __('Delete') }}</x-different.button>

                @unless ($request->user()->isGuest())
                    <x-different.export-form action="{{ route('manufacturers.export') }}" />
                @endunless
            </div>
        </div>

        @include('tables.default-template', ['tableName' => 'manufacturers'])
    </div>

    <x-modals.multiple-delete action="{{ route('manufacturers.destroy') }}" :force-delete="false" />
    <x-modals.edit-table-columns action="{{ route('settings.update-table-columns') }}" table="manufacturers" :columns="$allTableColumns" />
@endsection

@section('rightbar')
    @include('filters.manufacturers')
@endsection
