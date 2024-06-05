@switch($column['name'])
    {{-- Edit --}}
    @case('Edit')
        @include('tables.components.th.edit')
    @break

    @case('Status date')
        @include('tables.components.th.sort-link', ['orderBy' => 'status_update_date'])
    @break

    @case('Search country')
        @include('tables.components.th.sort-link', ['orderBy' => 'country_code_id'])
    @break

    @case('Product status')
        @include('tables.components.th.sort-link', ['orderBy' => 'status_id'])
    @break

    @case('MAH')
        @include('tables.components.th.sort-link', ['orderBy' => 'marketing_authorization_holder_id'])
    @break

    @case('Manufacturer price 1')
        @include('tables.components.th.sort-link', ['orderBy' => 'manufacturer_first_offered_price'])
    @break

    @case('Manufacturer price 2')
        @include('tables.components.th.sort-link', ['orderBy' => 'manufacturer_followed_offered_price'])
    @break

    @case('Currency')
        @include('tables.components.th.sort-link', ['orderBy' => 'currency_id'])
    @break

    @case('Agreed price')
        @include('tables.components.th.sort-link', ['orderBy' => 'agreed_price'])
    @break

    @case('Our price 2')
        @include('tables.components.th.sort-link', ['orderBy' => 'our_followed_offered_price'])
    @break

    @case('Our price 1')
        @include('tables.components.th.sort-link', ['orderBy' => 'our_first_offered_price'])
    @break

    @case('Increased price')
        @include('tables.components.th.sort-link', ['orderBy' => 'increased_price'])
    @break

    @case('Increased price %')
        @include('tables.components.th.sort-link', ['orderBy' => 'increased_price_percentage'])
    @break

    @case('Increased price date')
        @include('tables.components.th.sort-link', ['orderBy' => 'increased_price_date'])
    @break

    @case('Dossier status')
        @include('tables.components.th.sort-link', ['orderBy' => 'dossier_status'])
    @break

    @case('Year Cr/Be')
        @include('tables.components.th.sort-link', ['orderBy' => 'clinical_trial_year'])
    @break

    @case('Country ich')
        @include('tables.components.th.sort-link', ['orderBy' => 'clinical_trial_ich_country'])
    @break

    @case('Down payment 1')
        @include('tables.components.th.sort-link', ['orderBy' => 'down_payment_1'])
    @break

    @case('Down payment 2')
        @include('tables.components.th.sort-link', ['orderBy' => 'down_payment_2'])
    @break

    @case('Down payment condition')
        @include('tables.components.th.sort-link', ['orderBy' => 'down_payment_condition'])
    @break

    @case('Date of forecast')
        @include('tables.components.th.sort-link', ['orderBy' => 'forecast_year_1_update_date'])
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

    @case('Responsible date')
        @include('tables.components.th.sort-link', ['orderBy' => 'responsible_people_update_date'])
    @break

    @case('Brand Eng')
        @include('tables.components.th.sort-link', ['orderBy' => 'trademark_en'])
    @break

    @case('Brand Rus')
        @include('tables.components.th.sort-link', ['orderBy' => 'trademark_ru'])
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
