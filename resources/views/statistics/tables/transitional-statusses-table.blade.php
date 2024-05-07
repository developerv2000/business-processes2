<div class="statistics-index__table2-container styled-box">
    <div class="pre-content">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Table') . ' 2', __('Table 1 description')],
            'fullScreen' => false,
        ])
    </div>

    <table class="table main-table">
        {{-- Head start --}}
        <thead>
            <tr>
                <th>{{ __('Status') }}</th>

                @foreach ($monthes as $month)
                    <th>{{ __($month['name']) }}</th>
                @endforeach

                <th>{{ __('Total') }}</th>
            </tr>
        </thead> {{-- Head end --}}

        {{-- Body Start --}}
        <tbody>
            @foreach ($generalStatusses as $status)
                <tr>
                    <td>{{ $status->name }}</td>

                    @foreach ($status->monthes as $month)
                        <td>{{ $month['transitional_processes_count'] }}</td>
                    @endforeach

                    <td>{{ $status->total_transitional_processes_count }}</td>
                </tr>
            @endforeach

            {{-- Sum of total statusses --}}
            <tr>
                <td>{{ __('Total') }}</td>

                @foreach ($monthes as $month)
                    <td>{{ $month['total_transitional_processes_count'] }}</td>
                @endforeach

                <td>{{ $sumOfTotalTransitionalProcessesCount }}</td>
            </tr>
        </tbody> {{-- Body end --}}
    </table>
</div>
