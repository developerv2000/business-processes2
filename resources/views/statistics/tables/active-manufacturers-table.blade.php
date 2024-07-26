<div class="statistics-index__table3-container styled-box">
    <div class="pre-content pre-content--transparent">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Number of active manufacturers by month')],
            'fullScreen' => false,
        ])
    </div>

    <table class="table main-table">
        {{-- Head start --}}
        <thead>
            <tr>
                @foreach ($months as $month)
                    <th>{{ __($month['name']) }}</th>
                @endforeach

                <th>{{ __('Total') }}</th>
            </tr>
        </thead> {{-- Head end --}}

        {{-- Body Start --}}
        <tbody>
            <tr>
                @foreach ($months as $month)
                    <td>
                        <a href="{{ $month['active_manufacturers_link'] }}">
                            {{ $month['active_manufacturers_count'] }}
                        </a>
                    </td>
                @endforeach

                <td>{{ $yearActiveManufacturersCount }}</td>
            </tr>
        </tbody> {{-- Body end --}}
    </table>
</div>
