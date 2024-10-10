<table class="table main-table">
    {{-- Head start --}}
    <thead>
        <tr>
            @can('edit-users')
                <th width="60">
                    @include('tables.components.th.edit')
                </th>
            @endcan

            <th width="100">{{ __('Photo') }}</th>

            <th>
                @include('tables.components.th.static-sort-link', ['text' => 'Name', 'orderBy' => 'name'])
            </th>

            <th>{{ __('Roles') }}</th>

            <th>{{ __('Permissions') }}</th>

            <th>{{ __('Responsible countries') }}</th>

            <th>
                @include('tables.components.th.static-sort-link', ['text' => 'Email address', 'orderBy' => 'email'])
            </th>
        </tr>
    </thead> {{-- Head end --}}

    {{-- Body Start --}}
    <tbody>
        @foreach ($records as $instance)
            <tr>
                @can('edit-users')
                    <td>
                        @include('tables.components.td.edit-button', ['href' => route('users.edit', $instance->id)])
                    </td>
                @endcan

                <td>
                    <img class="td__image" src="{{ $instance->photo_asset_path }}">
                </td>

                <td>{{ $instance->name }}</td>

                <td>
                    <div class="td__categories">
                        @foreach ($instance->roles as $role)
                            <span class="badge badge--green">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </td>

                <td>
                    <div class="td__categories">
                        @foreach ($instance->permissions as $permission)
                            <span class="badge badge--blue">{{ $permission->name }}</span> <br>
                        @endforeach
                    </div>
                </td>

                <td>{{ $instance->responsibleCountries->pluck('name')->implode(' ') }}</td>

                <td>{{ $instance->email }}</td>
            </tr>
        @endforeach
    </tbody> {{-- Body end --}}
</table>

{{ $records->links('layouts.pagination') }}
