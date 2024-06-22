<div class="statistics-index__table1-container styled-box">
    <div class="pre-content">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Количество процессов, у которых текущий статус соответствует указанному статусу')],
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
                        <td>
                            <a href="{{ $month['current_processes_link'] }}">
                                {{ $month['current_processes_count'] }}

                                @if (!$request->extensive && $status->stage == 5)
                                    ({{ $month['year_based_current_processes_count'] }})
                                @endif
                            </a>
                        </td>
                    @endforeach

                    <td>{{ $status->total_current_processes_count }}</td>
                </tr>
            @endforeach

            {{-- Sum of total statuses --}}
            <tr>
                <td>{{ __('Total') }}</td>

                @foreach ($months as $month)
                    <td>{{ $month['total_current_processes_count'] }}</td>
                @endforeach

                <td>{{ $sumOfTotalCurrentProcessesCount }}</td>
            </tr>
        </tbody> {{-- Body end --}}
    </table>
</div>
