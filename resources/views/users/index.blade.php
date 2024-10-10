@extends('layouts.app', ['page' => 'users-index'])

@section('main')
    <div class="main__conent-box styled-box">
        <div class="pre-content pre-content--transparent">
            @include('layouts.breadcrumbs', [
                'crumbs' => [__('Filtered records') . ' - ' . $records->total()],
                'fullScreen' => true,
                'fullScreenSelector' => '.main-wrapper',
            ])

            @can('edit-users')
                <div class="pre-content__actions">
                    <x-different.linked-button style="action" icon="add" href="{{ route('users.create') }}">{{ __('New') }}</x-different.linked-button>
                </div>
            @endcan
        </div>

        @include('users.partials.table')
    </div>
@endsection
