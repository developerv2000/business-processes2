@switch($column['name'])
    @case('PO date')
        {{ $instance->purchase_order_date?->isoformat('DD MMM Y') }}
    @break

    @case('PO №')
        {{ $instance->purchase_order_name }}
    @break

    @case('Products')
        <a class="td__link td__link--margined" href="{{ route('order.products.index', ['order_id[]' => $instance->id]) }}">
            {{ $instance->products_count }} {{ __('products') }}
        </a>
    @break

    @case('Country')
        {{ $instance->country->name }}
    @break
@endswitch
