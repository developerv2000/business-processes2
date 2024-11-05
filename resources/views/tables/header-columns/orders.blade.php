@switch($column['name'])
    {{-- Edit --}}
    @case('Edit')
        @include('tables.components.th.edit')
    @break

    {{-- Links --}}
    @case('Receive date')
        @include('tables.components.th.sort-link', ['orderBy' => 'receive_date'])
    @break

    @case('PO date')
        @include('tables.components.th.sort-link', ['orderBy' => 'purchase_order_date'])
    @break

    @case('PO №')
        @include('tables.components.th.sort-link', ['orderBy' => 'purchase_order_name'])
    @break

    @case('Manufacturer')
        @include('tables.components.th.sort-link', ['orderBy' => 'manufacturer_id'])
    @break

    @case('Country')
        @include('tables.components.th.sort-link', ['orderBy' => 'country_code_id'])
    @break

    @case('Currency')
        @include('tables.components.th.sort-link', ['orderBy' => 'currency_id'])
    @break

    @case('Readiness date')
        @include('tables.components.th.sort-link', ['orderBy' => 'readiness_date'])
    @break

    @case('Expected dispatch date')
        @include('tables.components.th.sort-link', ['orderBy' => 'expected_dispatch_date'])
    @break

    @case('Confirmed')
        @include('tables.components.th.sort-link', ['orderBy' => 'is_confirmed'])
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
