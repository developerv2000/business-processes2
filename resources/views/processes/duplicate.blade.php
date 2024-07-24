@extends('layouts.app', ['page' => 'processes-duplicate'])

@section('main')
    <div class="pre-content pre-content--intended styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('VPS'), __('Duplicate'), '# ' . $instance->id],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Store') }}</x-different.button>
        </div>
    </div>

    <form class="form edit-form" action="{{ route('processes.duplicate') }}" id="edit-form" method="POST" enctype="multipart/form-data" data-on-submit="show-spinner">
        @csrf
        <input type="hidden" name="previous_url" value="{{ old('previous_url', url()->previous()) }}">

        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="process_id" value="{{ $instance->id }}">

        @include('processes.partials.about-product')

        <div class="form__section">
            {{-- All stages of statuses are avialabe only for admins --}}
            @if (request()->user()->isAdminOrModerator() || !$instance->status->generalStatus->visible_only_for_admins)
                <x-forms.id-based-single-select.instance-edit-select
                    class="statuses-selectize selectize--manually-initializable"
                    label="Product status"
                    name="status_id"
                    :options="$statuses"
                    :instance="$instance"
                    required />
            @endif

            <x-forms.boolean-select.default-select
                class="historical-process-selectize selectize--manually-initializable"
                label="Historical process"
                name="is_historical"
                required />
        </div>

        <div class="form__section historical-process-date-container">
            <x-forms.input.default-input
                type="date"
                label="Historical process date"
                name="historical_date" />
        </div>

        <div class="processes-duplicate__stage-inputs-container form">
            @include('processes.partials.edit-form-stage-inputs', [
                'stage' => $instance->status->generalStatus->stage,
                'instance' => $instance,
                'product' => $product,
                'duplicating' => true,
            ])
        </div>

        <x-forms.textarea.default-textarea
            label="Add new comment"
            name="comment" />

        <x-different.button class="form__submit" type="submit">{{ __('Store') }}</x-different.button>
    </form>
@endsection
