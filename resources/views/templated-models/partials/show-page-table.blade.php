@include('tables.style-validations')

<div class="table-wrapper thin-scrollbar">
    <table class="table main-table">
        {{-- Head start --}}
        <thead>
            <tr>
                @include('tables.components.th.select-all')

                <th width="40">
                    @include('tables.components.th.edit')
                </th>

                @if ($modelAttributes->contains('name'))
                    <th>{{ __('Name') }}</th>
                @endif

                @if ($modelAttributes->contains('parent_id'))
                    <th>{{ __('Parent') }}</th>
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
                        @include('tables.components.td.edit-button', ['href' => route('templated-models.edit', ['modelName' => $model['name'], 'id' => $instance->id])])
                    </td>

                    @if ($modelAttributes->contains('name'))
                        <td>{{ $instance->name }}</td>
                    @endif

                    @if ($modelAttributes->contains('parent_id'))
                        <td>{{ $instance->parent?->name }}</td>
                    @endif

                    <td>{{ $instance->usage_count }}</td>
                </tr>
            @endforeach
        </tbody> {{-- Body end --}}
    </table>
</div>

{{ $records->links('layouts.pagination') }}
