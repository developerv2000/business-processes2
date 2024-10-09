@extends('layouts.app', ['page' => 'comments-index'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [$title, __('All comments')],
            'fullScreen' => false,
        ])
    </div>

    <div class="comments-index__box styled-box">
        <h1 class="comments-index__title main-title">{{ __('All comments') }} - {{ $instance->comments->count() }}</h1>

        @can('edit-comments')
            @include('comments.partials.create-form')
        @endcan

        @include('comments.partials.list')
    </div>

    @can('edit-comments')
        <x-modals.target-delete action="{{ route('comments.destroy') }}" :force-delete="false" />
    @endcan
@endsection
