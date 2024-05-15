<div class="table-wrapper thin-scrollbar">
    <table class="table main-table">
        {{-- Head start --}}
        <thead>
            <tr>
                @include('tables.components.th.select-all')

                <th width="40">
                    @include('tables.components.th.edit')
                </th>

                @if ($modelTemplates->contains('named'))
                    <th>{{ __('Name') }}</th>
                @endif

                <th>{{ __('Usage count') }}</th>
            </tr>
        </thead> {{-- Head end --}}

        {{-- Body Start --}}
        <tbody>
            @foreach ($records as $instance)
                <tr>
                    @include('tables.components.td.checkbox')

                    <td>
                        @include('tables.components.td.edit-button', ['href' => route('templated-models.edit', ['model' => $model['name'], 'id' => $instance->id])])
                    </td>

                    @if ($modelTemplates->contains('named'))
                        <td>{{ $instance->name }}</td>
                    @endif

                    <td>0</td>
                </tr>
            @endforeach
        </tbody> {{-- Body end --}}
    </table>
</div>

{{ $records->links('layouts.pagination') }}
