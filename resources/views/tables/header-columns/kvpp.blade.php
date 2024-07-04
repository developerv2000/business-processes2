@switch($column['name'])
    {{-- Edit --}}
    @case('Edit')
        @include('tables.components.th.edit')
    @break

    @case('Source EU')
        @include('tables.components.th.sort-link', ['orderBy' => 'source_eu'])
    @break

    @case('Source IN')
        @include('tables.components.th.sort-link', ['orderBy' => 'source_in'])
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

    @case('Generic')
        @include('tables.components.th.sort-link', ['orderBy' => 'inn_id'])
    @break

    @case('Form')
        @include('tables.components.th.sort-link', ['orderBy' => 'form_id'])
    @break

    @case('Dosage')
        @include('tables.components.th.sort-link', ['orderBy' => 'dosage'])
    @break

    @case('PC')
        @include('tables.components.th.sort-link', ['orderBy' => 'marketing_authorization_holder_id'])
    @break

    @case('Forecast 1 year')
        @include('tables.components.th.sort-link', ['orderBy' => 'forecast_year_1'])
    @break

    @case('Forecast 2 year')
        @include('tables.components.th.sort-link', ['orderBy' => 'forecast_year_2'])
    @break

    @case('Forecast 3 year')
        @include('tables.components.th.sort-link', ['orderBy' => 'forecast_year_3'])
    @break

    @case('Portfolio manager')
        @include('tables.components.th.sort-link', ['orderBy' => 'portfolio_manager_id'])
    @break

    @case('Analyst')
        @include('tables.components.th.sort-link', ['orderBy' => 'analyst_user_id'])
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
