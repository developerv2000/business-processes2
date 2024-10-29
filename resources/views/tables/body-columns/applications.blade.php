@switch($column['name'])
    @case('Edit')
        @include('tables.components.td.edit-button', ['href' => route('applications.edit', $instance->id)])
    @break

    @case('PO №')
        {{ $instance->name }}
    @break

    @case('Orders')
        <a class="td__link" href="{{ route('orders.index', ['application_id[]' => $instance->id]) }}">
            {{ $instance->orders_count }} {{ __('orders') }}
        </a>
    @break

    @case('Manufacturer')
        {{ $instance->manufacturer->name }}
    @break

    @case('Country')
        {{ $instance->process->searchCountry->name }}
    @break

    @case('Brand Eng')
        {{ $instance->process->trademark_en }}
    @break

    @case('Brand Rus')
        {{ $instance->process->trademark_ru }}
    @break

    @case('MAH')
        {{ $instance->process->marketingAuthorizationHolder?->name }}
    @break

    @case('Date of creation')
        {{ $instance->created_at->isoformat('DD MMM Y') }}
    @break

    @case('ID')
        {{ $instance->id }}
    @break
@endswitch
