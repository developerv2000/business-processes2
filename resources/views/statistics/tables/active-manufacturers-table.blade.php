<div class="statistics-index__table3-container styled-box">
    <div class="pre-content">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('Active manufacturers count for the given month')],
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
            </tr>
        </thead> {{-- Head end --}}

        {{-- Body Start --}}
        <tbody>
            {{-- Sum of total statuses --}}
            <tr>
                @foreach ($months as $month)
                    <td>
                        <a href="{{ $month['active_manufacturers_link'] }}">
                            {{ $month['active_manufacturers_count'] }}
                        </a>
                    </td>
                @endforeach
            </tr>
        </tbody> {{-- Body end --}}
    </table>
</div>
