@switch($column['name'])
    @case('Edit')
        @include('tables.components.td.edit-button', ['href' => route('processes_for_order.edit', $instance->id)])
    @break

    @case('Brand name ENG')
        {{ $instance->fixed_trademark_en_for_order }}
    @break

    @case('Brand name RUS')
        {{ $instance->fixed_trademark_ru_for_order }}
    @break

    @case('Products')
        <a class="td__link" href="{{ route('order.products.index', ['fixed_trademark_en_for_order[]' => $instance->fixed_trademark_en_for_order]) }}">
            {{ $instance->ordered_products_count }} {{ __('products') }}
        </a>
    @break

    @case('Manufacturer')
        {{ $instance->manufacturer->name }}
    @break

    @case('Country')
        {{ $instance->searchCountry->name }}
    @break

    @case('VPS Brand Eng')
        {{ $instance->trademark_en }}
    @break

    @case('VPS Brand Rus')
        {{ $instance->trademark_ru }}
    @break

    @case('MAH')
        {{ $instance->marketingAuthorizationHolder?->name }}
    @break

    @case('Form')
        {{ $instance->product->form->name }}
    @break

    @case('Pack')
        {{ $instance->product->pack }}
    @break

    @case('Date of creation')
        {{ $instance->readiness_for_order_date?->isoformat('DD MMM Y') }}
    @break
@endswitch
