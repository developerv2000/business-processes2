<div class="statistics-index__table2-container styled-box">
    <div class="pre-content">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Максимальное количество процессов, которое определённый статус имел в течении месяца')],
            'fullScreen' => false,
        ])
    </div>

    <table class="table main-table">
        {{-- Head start --}}
        <thead>
            <tr>
                <th>{{ __('Status') }}</th>

                @foreach ($months as $month)
                    <th>{{ __($month['name']) }}</th>
                @endforeach

                <th>{{ __('Total') }}</th>
            </tr>
        </thead> {{-- Head end --}}

        {{-- Body Start --}}
        <tbody>
            @foreach ($generalStatuses as $status)
                <tr>
                    <td>{{ $status->name }}</td>

                    @foreach ($status->months as $month)
                        <td>{{ $month['maximum_processes_count'] }}</td>
                    @endforeach

                    <td>{{ $status->year_maximum_processes_count }}</td>
                </tr>
            @endforeach

            {{-- Sum of total statuses --}}
            <tr>
                <td>{{ __('Total') }}</td>

                @foreach ($months as $month)
                    <td>{{ $month['all_maximum_process_count'] }}</td>
                @endforeach

                <td>{{ $yearTotalMaximumProcessesCount }}</td>
            </tr>
        </tbody> {{-- Body end --}}
    </table>
</div>
