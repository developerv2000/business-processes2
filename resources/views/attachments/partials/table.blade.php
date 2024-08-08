<table class="table main-table">
    {{-- Head start --}}
    <thead>
        <tr>
            @include('tables.components.th.select-all')

            <th>{{ __('File name') }}</th>
            <th>{{ __('File size') }}</th>
            <th>{{ __('Date') }}</th>
        </tr>
    </thead> {{-- Head end --}}

    {{-- Body Start --}}
    <tbody>
        @foreach ($records as $instance)
            <tr>
                @include('tables.components.td.checkbox')

                <td>
                    <a class="td__link" href="{{ asset($instance->file_path) }}">
                        {{ $instance->file_name }}
                    </a>
                </td>

                <td>{{ $instance->file_size_in_megabytes }} мб</td>
                <td>{{ $instance->created_at->isoformat('DD MMM Y HH:mm:ss') }}</td>
            </tr>
        @endforeach
    </tbody> {{-- Body end --}}
</table>
