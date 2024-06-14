<table class="table main-table">
    {{-- Head start --}}
    <thead>
        <tr>
            <th width="60">@include('tables.components.th.edit')</th>
            <th>{{ __('Product status') }}</th>
            <th>{{ __('General status') }}</th>
            <th>{{ __('Start date') }}</th>
            <th>{{ __('End date') }}</th>
            <th>{{ __('Duration days') }}</th>
        </tr>
    </thead> {{-- Head end --}}

    {{-- Body Start --}}
    <tbody>
        @foreach ($records as $record)
            <tr>
                <td>@include('tables.components.td.edit-button', ['href' => route('process-status-history.edit', [$process->id, $record->id])])</td>
                <td>{{ $record->status->name }}</td>
                <td>{{ $record->status->generalStatus->name }}</td>
                <td>{{ $record->start_date->isoformat('DD MMM Y HH:mm:ss') }}</td>
                <td>{{ $record->end_date?->isoformat('DD MMM Y HH:mm:ss') }}</td>
                <td>{{ $record->duration_days }}</td>
            </tr>
        @endforeach
    </tbody> {{-- Body end --}}
</table>
