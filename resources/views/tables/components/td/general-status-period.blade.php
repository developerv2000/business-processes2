<div class="status-periods">
    <div class="status-periods__duration">
        {{ $instance->general_statuses_with_periods[$stage - 1]->duration_days ?? '0' }} {{ __('days') }}
    </div>

    <hr
        class="status-periods__hr general-status-{{ $stage }}"
        style="width: {{ $instance->general_statuses_with_periods[$stage - 1]->duration_days_ratio }}%">

    <div class="status-periods__period">
        {{ ($instance->general_statuses_with_periods[$stage - 1]->start_date)?->isoFormat('DD/MM/YYYY') . ' - ' .
            ($instance->general_statuses_with_periods[$stage - 1]->end_date)?->isoFormat('DD/MM/YYYY') }}
    </div>
</div>
