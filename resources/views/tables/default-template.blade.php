@include('tables.style-validations')

<div class="table-wrapper thin-scrollbar">
    <table class="table">
        {{-- Head start --}}
        <thead>
            <tr>
                @include('tables.components.th.select-all')

                @foreach ($visibleTableColumns as $column)
                    <th width="{{ $column['width'] }}">
                        @include('tables.header-columns.' . $tableName)
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
                            @include('tables.body-columns.' . $tableName)
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody> {{-- Body end --}}
    </table>
</div>

{{ $items->links('layouts.pagination') }}
