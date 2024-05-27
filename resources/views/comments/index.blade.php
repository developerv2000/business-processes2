@extends('layouts.app', ['page' => 'comments-index'])

@section('main')
    <div class="pre-content pre-content--intended styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [$title, __('All comments')],
            'fullScreen' => false,
        ])
    </div>

    <div class="comments-index__box styled-box">
        <h1 class="comments-index__title main-title">{{ __('All comments') }} - {{ $instance->comments->count() }}</h1>

        @include('comments.partials.create-form')
        @include('comments.partials.list')
    </div>

    @if (request()->user()->isAdminOrModerator())
        <x-modals.target-delete action="{{ route('comments.destroy') }}" :force-delete="false" />
    @endif
@endsection
