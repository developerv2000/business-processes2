@extends('layouts.app', ['page' => 'processes-for-order-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('Filtered records') . ' - ' . $records->total()],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])

            <div class="pre-content__actions">
                <x-different.button style="action" icon="view_column" data-click-action="show-modal" data-modal-selector=".edit-table-columns-modal">{{ __('Columns') }}</x-different.button>
            </div>
        </div>

        @include('tables.default-template', ['tableName' => 'processes-for-order'])
    </div>

    <x-modals.edit-table-columns action="{{ route('settings.update-table-columns') }}" table="processes-for-order" :columns="$allTableColumns" />
@endsection

@section('rightbar')
    @include('filters.processes-for-order')
@endsection
