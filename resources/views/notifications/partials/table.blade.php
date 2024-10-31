<table class="table main-table">
    {{-- Head start --}}
    <thead>
        <tr>
            @include('tables.components.th.select-all')

            <th width="200">{{ __('Date') }}</th>
            <th width="140">{{ __('Status') }}</th>
            <th>{{ __('Text') }}</th>
        </tr>
    </thead> {{-- Head end --}}

    {{-- Body Start --}}
    <tbody>
        @foreach ($records as $instance)
            <tr>
                @include('tables.components.td.checkbox')

                <td>{{ $instance->created_at->isoformat('DD MMM Y HH:mm:ss') }}</td>

                <td>
                    @if ($instance->read_at)
                        {{ __('Read') }}
                    @else
                        <span class="badge badge--pink">{{ __('Unread') }}</span>
                    @endif
                </td>

                <td>
                    @include('notifications.partials.text', ['instance' => $instance])
                </td>
            </tr>
        @endforeach
    </tbody> {{-- Body end --}}
</table>

{{ $records->links('layouts.pagination') }}
