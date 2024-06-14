@switch($column['name'])
    @case('Edit')
        @include('tables.components.td.edit-button', ['href' => route('processes.edit', $instance->id)])
    @break

    @case('Status date')
        {{ $instance->status_update_date->isoformat('DD MMM Y') }}
    @break

    @case('Search country')
        {{ $instance->searchCountry->name }}
    @break

    @case('Product status')
        {{ $instance->status->name }}
    @break

    @case('Product status An*')
        {{ $instance->status->generalStatus->name_for_analysts }}
    @break

    @case('General status')
        {{ $instance->status->generalStatus->name }}
    @break

    @case('Manufacturer category')
        <span @class([
            'badge',
            'badge--yellow' => $instance->manufacturer->category->name == 'УДС',
            'badge--pink' => $instance->manufacturer->category->name == 'НПП',
        ])>
            {{ $instance->manufacturer->category->name }}
        </span>
    @break

    @case('Manufacturer')
        {{ $instance->manufacturer->name }}
    @break

    @case('Manufacturer country')
        {{ $instance->manufacturer->country->name }}
    @break

    @case('BDM')
        <x-different.ava image="{{ $instance->manufacturer->bdm->photo_asset_path }}" title="{{ $instance->manufacturer->bdm->name }}" />
    @break

    @case('Analyst')
        <x-different.ava image="{{ $instance->manufacturer->analyst->photo_asset_path }}" title="{{ $instance->manufacturer->analyst->name }}" />
    @break

    @case('Generic')
        @include('tables.components.td.limited-text', ['text' => $instance->product->inn->name])
    @break

    @case('Form')
        {{ $instance->product->form->name }}
    @break

    @case('Dosage')
        @include('tables.components.td.limited-text', ['text' => $instance->product->dosage])
    @break

    @case('Pack')
        {{ $instance->product->pack }}
    @break

    @case('MAH')
        {{ $instance->marketingAuthorizationHolder?->name }}
    @break

    @case('Comments')
        @include('tables.components.td.all-comments-link')
    @break

    @case('Last comment')
        @include('tables.components.td.limited-text', ['text' => $instance->lastComment?->body])
    @break

    @case('Comments date')
        {{ $instance->lastComment?->created_at->isoformat('DD MMM Y') }}
    @break

    @case('Manufacturer price 1')
        {{ $instance->manufacturer_first_offered_price }}
    @break

    @case('Manufacturer price 2')
        {{ $instance->manufacturer_followed_offered_price }}
    @break

    @case('Currency')
        {{ $instance->currency?->name }}
    @break

    @case('Price in USD')
        {{ $instance->manufacturer_followed_offered_price_in_usd }}
    @break

    @case('Agreed price')
        {{ $instance->agreed_price }}
    @break

    @case('Our price 2')
        {{ $instance->our_followed_offered_price }}
    @break

    @case('Our price 1')
        {{ $instance->our_first_offered_price }}
    @break

    @case('Increased price')
        {{ $instance->increased_price }}
    @break

    @case('Increased price %')
        {{ $instance->increased_price_percentage . ($instance->increased_price_percentage ? ' %' : '') }}
    @break

    @case('Increased price date')
        {{ $instance->increased_price_date?->isoformat('DD MMM Y') }}
    @break

    @case('Shelf life')
        {{ $instance->product->shelfLife->name }}
    @break

    @case('MOQ')
        {{ $instance->product->moq }}
    @break

    @case('Dossier status')
        {{ $instance->dossier_status }}
    @break

    @case('Year Cr/Be')
        {{ $instance->clinical_trial_year }}
    @break

    @case('Countries Cr/Be')
        @foreach ($instance->clinicalTrialCountries as $country)
            {{ $country->name }}<br>
        @endforeach
    @break

    @case('Country ich')
        {{ $instance->clinical_trial_ich_country }}
    @break

    @case('Zones')
        @foreach ($instance->product->zones as $zone)
            {{ $zone->name }}<br>
        @endforeach
    @break

    @case('Down payment 1')
        {{ $instance->down_payment_1 }}
    @break

    @case('Down payment 2')
        {{ $instance->down_payment_2 }}
    @break

    @case('Down payment condition')
        {{ $instance->down_payment_condition }}
    @break

    @case('Date of forecast')
        {{ $instance->forecast_year_1_update_date?->isoformat('DD MMM Y') }}
    @break

    @case('Forecast 1 year')
        @include('tables.components.td.formatted-price', ['attribute' => 'forecast_year_1'])
    @break

    @case('Forecast 2 year')
        @include('tables.components.td.formatted-price', ['attribute' => 'forecast_year_2'])
    @break

    @case('Forecast 3 year')
        @include('tables.components.td.formatted-price', ['attribute' => 'forecast_year_3'])
    @break

    @case('Responsible')
        @foreach ($instance->responsiblePeople as $person)
            {{ $person->name }}<br>
        @endforeach
    @break

    @case('Responsible update date')
        {{ $instance->responsible_people_update_date?->isoformat('DD MMM Y') }}
    @break

    @case('Days have passed')
        {{ $instance->days_past }}
    @break

    @case('Brand Eng')
        {{ $instance->trademark_en }}
    @break

    @case('Brand Rus')
        {{ $instance->trademark_ru }}
    @break

    @case('Date of creation')
        {{ $instance->created_at->isoformat('DD MMM Y') }}
    @break

    @case('Update date')
        {{ $instance->updated_at->isoformat('DD MMM Y') }}
    @break

    @case('Product class')
        <span class="badge badge--green">{{ $instance->product->class->name }}</span>
    @break

    @case('ID')
        {{ $instance->id }}
    @break

    @case('History')
        @include('tables.components.td.edit-button', ['href' => route('process-status-history.index', $instance->id)])
    @break

    @case('ВП')
        @include('tables.components.td.general-status-period', ['stage' => 1])
    @break

    @case('ПО')
        @include('tables.components.td.general-status-period', ['stage' => 2])
    @break

    @case('АЦ')
        @include('tables.components.td.general-status-period', ['stage' => 3])
    @break

    @case('СЦ')
        @include('tables.components.td.general-status-period', ['stage' => 4])
    @break

    @case('Кк')
        @include('tables.components.td.general-status-period', ['stage' => 5])
    @break

    @case('КД')
        @include('tables.components.td.general-status-period', ['stage' => 6])
    @break

    @case('НПР')
        @include('tables.components.td.general-status-period', ['stage' => 7])
    @break

    @case('Р')
        @include('tables.components.td.general-status-period', ['stage' => 8])
    @break

    @case('Зя')
        @include('tables.components.td.general-status-period', ['stage' => 9])
    @break

    @case('Отмена')
        @include('tables.components.td.general-status-period', ['stage' => 10])
    @break

@endswitch
