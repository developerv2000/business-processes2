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
