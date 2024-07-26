@extends('layouts.app', ['page' => 'processes-edit'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('VPS'), __('Edit'), '# ' . $instance->id],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.edit-template action="{{ route('processes.update', $instance->id) }}">
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
            @else
                {{-- Else display readonly status name if current status stage is not available for current user --}}
                <x-forms.input.default-input
                    label="Product status"
                    :value="$instance->status->generalStatus->name"
                    name="readonly"
                    readonly />
            @endif
        </div>

        <div class="processes-edit__stage-inputs-container form">
            @include('processes.partials.edit-form-stage-inputs', [
                'stage' => $instance->status->generalStatus->stage,
                'instance' => $instance,
                'product' => $product,
                'duplicating' => false,
            ])
        </div>

        @include('comments.model-form-partials.edit-form-fields')
    </x-forms.template.edit-template>
@endsection
