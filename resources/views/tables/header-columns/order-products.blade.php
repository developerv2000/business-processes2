@switch($column['name'])
    {{-- Edit --}}
    @case('Edit')
        @include('tables.components.th.edit')
    @break

    {{-- Links --}}
    @case('PO №')
        @include('tables.components.th.sort-link', ['orderBy' => 'order_id'])
    @break

    @case('Country')
        @include('tables.components.th.sort-link', ['orderBy' => 'country_code_id'])
    @break

    @case('MAH')
        @include('tables.components.th.sort-link', ['orderBy' => 'marketing_authorization_holder_id'])
    @break

    @case('Quantity')
        @include('tables.components.th.sort-link', ['orderBy' => 'quantity'])
    @break

    @case('Price')
        @include('tables.components.th.sort-link', ['orderBy' => 'price'])
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
