@extends('layouts.app', ['page' => 'invoices-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('Filtered records') . ' - ' . $records->count()],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])

            <div class="pre-content__actions">
                <x-different.linked-button style="action" icon="add" href="{{ route('invoices.create.goods') }}">{{ __('Add Goods') }}</x-different.linked-button>
                <x-different.linked-button style="action" icon="add" href="{{ route('invoices.create.services') }}">{{ __('Add Service') }}</x-different.linked-button>

                <x-different.button style="action" icon="remove" data-click-action="show-modal" data-modal-selector=".multiple-delete-modal">{{ __('Delete') }}</x-different.button>
            </div>
        </div>

        @include('invoices.partials.table')
    </div>

    <x-modals.multiple-delete action="{{ route('invoices.destroy') }}" :force-delete="false" />
@endsection
