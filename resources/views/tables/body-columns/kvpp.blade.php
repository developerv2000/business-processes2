@switch($column['name'])
    @case('Edit')
        @include('tables.components.td.edit-button', ['href' => route('kvpp.edit', $instance->id)])
    @break

    @case('Status')
        {{ $instance->status->name }}
    @break

    @case('Country')
        {{ $instance->country->name }}
    @break

    @case('Priority')
        <span @class([
            'badge',
            'badge--red' => $instance->priority->name == 'A',
            'badge--green' => $instance->priority->name == 'B',
            'badge--yellow' => $instance->priority->name == 'C',
        ])>
            {{ $instance->priority->name }}
        </span>
    @break

    @case('VPS coincidents')
        @foreach ($instance->coincident_processes as $coincidentProcess)
            <a class="td__link" @if ($request->user()->isAdmin()) href="{{ route('processes.index', ['id' => $coincidentProcess->id]) }}" @endif>
                # {{ $coincidentProcess->id }} - {{ $coincidentProcess->status->name }}
            </a><br>
        @endforeach
    @break

    @case('IVP coincidents')
        <a class="td__link" href="{{ route('products.index', ['inn_id' => $instance->inn_id, 'form_id' => $instance->form_id]) }}">
            {{ $instance->coincident_products_count }} {{ __('products') }}
        </a><br>
    @break

    @case('Source')
        {{ $instance->source->name }}
    @break

    @case('Generic')
        @include('tables.components.td.limited-text', ['text' => $instance->inn->name])
    @break

    @case('Form')
        {{ $instance->form->name }}
    @break

    @case('Basic form')
        {{ $instance->form->parent_name }}
    @break

    @case('Dosage')
        @include('tables.components.td.limited-text', ['text' => $instance->dosage])
    @break

    @case('Pack')
        {{ $instance->pack }}
    @break

    @case('MAH')
        {{ $instance->marketingAuthorizationHolder->name }}
    @break

    @case('Information')
        @include('tables.components.td.limited-text', ['text' => $instance->information])
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

    @case('Date of forecast')
        {{ $instance->date_of_forecast?->isoformat('DD MMM Y') }}
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

    @case('Portfolio manager')
        {{ $instance->portfolioManager?->name }}
    @break

    @case('Analyst')
        @if ($instance->analyst)
            <x-different.ava image="{{ $instance->analyst->photo_asset_path }}" title="{{ $instance->analyst->name }}" />
        @endif
    @break

    @case('Date of creation')
        {{ $instance->created_at->isoformat('DD MMM Y') }}
    @break

    @case('Update date')
        {{ $instance->updated_at->isoformat('DD MMM Y') }}
    @break

    @case('ID')
        {{ $instance->id }}
    @break

@endswitch
