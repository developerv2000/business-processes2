<table class="table main-table">
    {{-- Head start --}}
    <thead>
        <tr>
            @include('tables.components.th.select-all')

            <th>{{ __('Date') }}</th>
            <th>{{ __('Text') }}</th>
            <th>{{ __('Status') }}</th>
        </tr>
    </thead> {{-- Head end --}}

    {{-- Body Start --}}
    <tbody>
        @foreach ($records as $instance)
            <tr>
                @include('tables.components.td.checkbox')

                <td>{{ $instance->created_at->isoformat('DD MMM Y HH:mm:ss') }}</td>

                <td>
                    @include('notifications.partials.text', ['instance' => $instance])
                </td>

                <td>
                    @if ($instance->read_at)
                        {{ __('Прочитано') }}
                    @else
                        <span class="badge badge--pink">{{ __('New') }}</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody> {{-- Body end --}}
</table>

{{ $records->links('layouts.pagination') }}
