@extends('layouts.app', ['page' => 'attachments-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('Attachments') . ' - ' . $records->count()],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])

            <div class="pre-content__actions">
                <x-different.button style="action" icon="remove" data-click-action="show-modal" data-modal-selector=".multiple-delete-modal">{{ __('Delete') }}</x-different.button>
            </div>
        </div>

        @include('attachments.partials.table')
        <x-modals.multiple-delete action="{{ route('attachments.destroy') }}" :force-delete="false" />
    </div>
@endsection
