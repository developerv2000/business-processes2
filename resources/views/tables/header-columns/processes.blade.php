@switch($column['name'])
    {{-- Edit --}}
    @case('Edit')
        @include('tables.components.th.edit')
    @break

    @case('Date of creation')
        @include('tables.components.th.sort-link', ['orderBy' => 'created_at'])
    @break

    @case('Update date')
        @include('tables.components.th.sort-link', ['orderBy' => 'updated_at'])
    @break

    {{-- Static title text --}}

    @default
        @include('tables.components.th.unlinked-title')
    @break
@endswitch
