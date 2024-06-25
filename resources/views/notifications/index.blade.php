@extends('layouts.app', ['page' => 'notifications-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('Notifications') . ' - ' . $records->total()],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])

            <div class="pre-content__actions">
                @include('notifications.partials.mark-as-read-form')

                <x-different.button style="action" icon="remove" data-click-action="show-modal" data-modal-selector=".multiple-delete-modal">{{ __('Delete') }}</x-different.button>
            </div>
        </div>

        @include('notifications.partials.table')
        <x-modals.multiple-delete action="{{ route('notifications.destroy') }}" :force-delete="false" />
    </div>
@endsection
