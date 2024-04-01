@extends('layouts.app', ['page' => 'profile-edit'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('My profile'), __('Edit')],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button icon="done_outline" style="action" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>
@endsection
