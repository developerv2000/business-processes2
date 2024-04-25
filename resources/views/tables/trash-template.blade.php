@include('tables.style-validations')

@php
    $tableHeaderColumnsPath = 'tables.header-columns.' . $tableName;
    $tableBodyColumnsPath = 'tables.body-columns.' . $tableName;
@endphp

<div class="table-wrapper thin-scrollbar">
    <table class="table main-table">
        {{-- Head start --}}
        <thead>
            <tr>
                @include('tables.components.th.select-all')

                <th width="130">
                    @include('tables.components.th.static-sort-link', ['text' => 'Deletion date', 'orderBy' => 'deleted_at'])
                </th>

                @foreach ($visibleTableColumns as $column)
                    <th width="{{ $column['width'] }}">
                        @include($tableHeaderColumnsPath)
                    </th>
                @endforeach
            </tr>
        </thead> {{-- Head end --}}

        {{-- Body Start --}}
        <tbody>
            @foreach ($records as $instance)
                <tr>
                    @include('tables.components.td.checkbox')

                    <td>{{ $instance->deleted_at->isoformat('DD MMM Y') }}</td>

                    @foreach ($visibleTableColumns as $column)
                        <td>
                            @include($tableBodyColumnsPath)
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody> {{-- Body end --}}
    </table>
</div>

{{ $records->links('layouts.pagination') }}
