<table class="table main-table">
    {{-- Head start --}}
    <thead>
        <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Permissions') }}</th>
        </tr>
    </thead> {{-- Head end --}}

    {{-- Body Start --}}
    <tbody>
        @foreach ($records as $instance)
            <tr>
                <td>{{ $instance->name }}</td>

                <td>
                    @switch($instance->name)
                        @case('Administrator')
                            <span class="badge badge--blue">Full access</span>
                        @break

                        @case('Analyst')
                            <span class="badge badge--blue">Analyst</span>
                        @break

                        @case('BDM')
                            <span class="badge badge--blue">BDM</span>
                        @break

                        @case('Inactive')
                            <span class="badge badge--blue">can`t login</span>
                        @break

                        @default
                            <div class="td__categories">
                                @foreach ($instance->permissions as $role)
                                    <span class="badge badge--blue">{{ $role->name }}</span>
                                @endforeach
                            </div>
                    @endswitch
                </td>
            </tr>
        @endforeach
    </tbody> {{-- Body end --}}
</table>
