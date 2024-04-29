@switch($column['name'])
    {{-- Edit --}}
    @case('Edit')
        @include('tables.components.th.edit')
    @break

    {{-- Links --}}
    @case('Manufacturer')
        @include('tables.components.th.sort-link', ['orderBy' => 'manufacturer_id'])
    @break

    @case('Manufacturer Brand')
        @include('tables.components.th.sort-link', ['orderBy' => 'brand'])
    @break

    @case('Generic')
        @include('tables.components.th.sort-link', ['orderBy' => 'inn_id'])
    @break

    @case('Form')
        @include('tables.components.th.sort-link', ['orderBy' => 'form_id'])
    @break

    @case('Shelf life')
        @include('tables.components.th.sort-link', ['orderBy' => 'shelf_life_id'])
    @break

    @case('Down payment')
        @include('tables.components.th.sort-link', ['orderBy' => 'down_payment'])
    @break

    @case('Registered in EU')
        @include('tables.components.th.sort-link', ['orderBy' => 'registered_in_eu'])
    @break

    @case('Sold in EU')
        @include('tables.components.th.sort-link', ['orderBy' => 'sold_in_eu'])
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
