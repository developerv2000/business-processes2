@extends('layouts.app', ['page' => 'products-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('Filtered records') . ' - ' . $records->total()],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])

            <div class="pre-content__actions">
                <x-different.linked-button style="action" icon="add" href="{{ route('products.create') }}">{{ __('New') }}</x-different.linked-button>
                <x-different.linked-button style="action" icon="delete" href="{{ route('products.trash') }}">{{ __('Trash') }}</x-different.linked-button>

                <x-different.button style="action" icon="view_column" data-click-action="show-modal" data-modal-selector=".edit-table-columns-modal">{{ __('Columns') }}</x-different.button>
                <x-different.button style="action" icon="remove" data-click-action="show-modal" data-modal-selector=".multiple-delete-modal">{{ __('Delete') }}</x-different.button>

                @unless ($request->user()->isTrainee())
                    <x-different.export-form action="{{ route('products.export') }}" />
                @endunless

                {{-- Export VP --}}
                @if (!$request->user()->isTrainee() && $request->manufacturer_id && count($request->manufacturer_id) == 1)
                    <x-different.export-products-vp-form :manufacturer-id="($request->manufacturer_id)[0]" />
                @endif
            </div>
        </div>

        @include('tables.default-template', ['tableName' => 'products'])
    </div>

    <x-modals.multiple-delete action="{{ route('products.destroy') }}" :force-delete="false" />
    <x-modals.edit-table-columns action="{{ route('settings.update-table-columns') }}" table="products" :columns="$allTableColumns" />
@endsection

@section('rightbar')
    @include('filters.products')
@endsection
