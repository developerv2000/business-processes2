@extends('layouts.app', ['page' => 'products-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('Filtered records') . ' - ' . $records->total()],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])

            <div class="pre-content__actions">
                @can('edit-ivp')
                    <x-different.linked-button style="action" icon="add" href="{{ route('products.create') }}">{{ __('New') }}</x-different.linked-button>
                @endcan

                <x-different.linked-button style="action" icon="delete" href="{{ route('products.trash') }}">{{ __('Trash') }}</x-different.linked-button>

                <x-different.button style="action" icon="view_column" data-click-action="show-modal" data-modal-selector=".edit-table-columns-modal">{{ __('Columns') }}</x-different.button>

                @can('edit-ivp')
                    <x-different.button style="action" icon="remove" data-click-action="show-modal" data-modal-selector=".multiple-delete-modal">{{ __('Delete') }}</x-different.button>
                @endcan

                @can('export-as-excel')
                    <x-different.export-form action="{{ route('products.export') }}" />
                    <x-different.products-selection-form model="Product" />
                @endcan
            </div>
        </div>

        @include('tables.default-template', ['tableName' => 'products'])
    </div>

    @can('edit-ivp')
        <x-modals.multiple-delete action="{{ route('products.destroy') }}" :force-delete="false" />
    @endcan

    <x-modals.edit-table-columns action="{{ route('settings.update-table-columns') }}" table="products" :columns="$allTableColumns" />
@endsection

@section('rightbar')
    @include('filters.products')
@endsection
