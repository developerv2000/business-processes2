@include('tables.style-validations')

@php
    $tableHeaderColumnsPath = 'tables.header-columns.' . $tableName;
    $tableBodyColumnsPath = 'tables.body-columns.' . $tableName;
@endphp

<div class="table-wrapper thin-scrollbar">
    <table class="table">
        {{-- Head start --}}
        <thead>
            <tr>
                @include('tables.components.th.select-all')

                <th width="130">
                    @include('tables.components.th.static-sort-link', ['text' => 'Deletion date', 'orderBy' => 'deleted_at'])
                </th>

                <th width="44">
                    @include('tables.components.th.iconed-title', ['title' => 'Restore', 'icon' => 'history'])
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
            @foreach ($items as $item)
                <tr>
                    @include('tables.components.td.checkbox')

                    <td>{{ $item->deleted_at->isoformat('DD MMM Y') }}</td>

                    @include('tables.components.td.restore-button')

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

{{ $items->links('layouts.pagination') }}
