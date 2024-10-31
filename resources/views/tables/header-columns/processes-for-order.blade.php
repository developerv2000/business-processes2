@switch($column['name'])
    {{-- Edit --}}
    @case('Edit')
        @include('tables.components.th.edit')
    @break

    {{-- Links --}}
    @case('Brand name ENG')
        @include('tables.components.th.sort-link', ['orderBy' => 'fixed_trademark_en_for_order'])
    @break

    @case('Brand name RUS')
        @include('tables.components.th.sort-link', ['orderBy' => 'fixed_trademark_ru_for_order'])
    @break

    @case('Products')
        @include('tables.components.th.sort-link', ['orderBy' => 'products_count'])
    @break

    @case('Country')
        @include('tables.components.th.sort-link', ['orderBy' => 'country_code_id'])
    @break

    @case('VPS Brand Eng')
        @include('tables.components.th.sort-link', ['orderBy' => 'trademark_en'])
    @break

    @case('VPS Brand Rus')
        @include('tables.components.th.sort-link', ['orderBy' => 'trademark_ru'])
    @break

    @case('MAH')
        @include('tables.components.th.sort-link', ['orderBy' => 'marketing_authorization_holder_id'])
    @break

    @case('Date of creation')
        @include('tables.components.th.sort-link', ['orderBy' => 'created_at'])
    @break

    {{-- Static title text --}}

    @default
        @include('tables.components.th.unlinked-title')
    @break
@endswitch
