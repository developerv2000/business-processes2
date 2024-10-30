@switch($column['name'])
    {{-- Edit --}}
    @case('Edit')
        @include('tables.components.th.edit')
    @break

    {{-- Links --}}
    @case('Orders')
        @include('tables.components.th.sort-link', ['orderBy' => 'orders_count'])
    @break

    @case('Date of creation')
        @include('tables.components.th.sort-link', ['orderBy' => 'created_at'])
    @break

    {{-- Static title text --}}

    @default
        @include('tables.components.th.unlinked-title')
    @break
@endswitch
