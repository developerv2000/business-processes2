@switch($column['name'])
    {{-- Edit --}}
    @case('Edit')
        @include('tables.components.th.edit')
    @break

    {{-- Links --}}
    @case('BDM')
        @include('tables.components.th.sort-link', ['orderBy' => 'bdm_user_id'])
    @break

    @case('Analyst')
        @include('tables.components.th.sort-link', ['orderBy' => 'analyst_user_id'])
    @break

    @case('Country')
        @include('tables.components.th.sort-link', ['orderBy' => 'country_id'])
    @break

    @case('Manufacturer')
        @include('tables.components.th.sort-link', ['orderBy' => 'name'])
    @break

    @case('Category')
        @include('tables.components.th.sort-link', ['orderBy' => 'category_id'])
    @break

    @case('Status')
        @include('tables.components.th.sort-link', ['orderBy' => 'is_active'])
    @break

    @case('Important')
        @include('tables.components.th.sort-link', ['orderBy' => 'is_important'])
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
