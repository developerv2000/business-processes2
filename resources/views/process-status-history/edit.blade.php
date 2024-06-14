@extends('layouts.app', ['page' => 'process-status-history-edit'])

@section('main')
    <div class="pre-content pre-content--intended styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Process') . ' #' . $process->id, __('Status history'), __('Edit'), '#' . $instance->id],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="remove" data-click-action="show-modal" data-modal-selector=".single-delete-modal">{{ __('Delete') }}</x-different.button>
        </div>
    </div>

    <x-errors.single name="process_status_history_deletion" />

    <x-forms.template.edit-template action="{{ route('process-status-history.update', [$process->id, $instance->id]) }}">
        <div class="form__section">
            {{-- status_id can not be edited for active status history --}}
            @unless ($instance->isActiveStatusHistory())
                <x-forms.id-based-single-select.instance-edit-select
                    label="Product status"
                    name="status_id"
                    :options="$statuses"
                    :instance="$instance"
                    required />
            @endunless

            <x-forms.input.instance-edit-input
                label="{{ __('Start date') }}"
                name="start_date"
                :instance="$instance"
                required />

            {{-- end_date can not be edited for active status history --}}
            @unless ($instance->isActiveStatusHistory())
                <x-forms.input.instance-edit-input
                    label="{{ __('End date') }}"
                    name="end_date"
                    :instance="$instance"
                    required />
            @endunless
        </div>
    </x-forms.template.edit-template>

    <x-modals.single-delete action="{{ route('process-status-history.destroy', [$process->id]) }}" :instance-id="$instance->id" :force-delete="false" />
@endsection
