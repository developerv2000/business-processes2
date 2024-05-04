@switch($column['name'])
    {{-- Edit --}}
    @case('Edit')
        @include('tables.components.th.edit')
    @break

    @case('Status')
        @include('tables.components.th.sort-link', ['orderBy' => 'status_id'])
    @break

    @case('Country')
        @include('tables.components.th.sort-link', ['orderBy' => 'country_code_id'])
    @break

    @case('Priority')
        @include('tables.components.th.sort-link', ['orderBy' => 'priority_id'])
    @break

    @case('Source')
        @include('tables.components.th.sort-link', ['orderBy' => 'source_id'])
    @break

    @case('MAH')
        @include('tables.components.th.sort-link', ['orderBy' => 'marketing_authorization_holder_id'])
    @break

    @case('Date of forecast')
        @include('tables.components.th.sort-link', ['orderBy' => 'date_of_forecast'])
    @break

    @case('Portfolio manager')
        @include('tables.components.th.sort-link', ['orderBy' => 'portfolio_manager_id'])
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
