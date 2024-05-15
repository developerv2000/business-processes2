<table class="table main-table">
    {{-- Head start --}}
    <thead>
        <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Records') }}</th>
        </tr>
    </thead> {{-- Head end --}}

    {{-- Body Start --}}
    <tbody>
        @foreach ($models as $model)
            <tr>
                <td>
                    <a class="td__link" href="{{ route('templated-models.show', $model['name']) }}">{{ $model['name'] }}</a>
                </td>

                <td>{{ $model['items_count'] }}</td>
            </tr>
        @endforeach
    </tbody> {{-- Body end --}}
</table>
