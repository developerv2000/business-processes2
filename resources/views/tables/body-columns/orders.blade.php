@switch($column['name'])
    @case('Edit')
        @include('tables.components.td.edit-button', ['href' => route('orders.edit', $instance->id)])
    @break

    @case('Receive date')
        {{ $instance->receive_date?->isoformat('DD MMM Y') }}
    @break

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

        <x-different.arrowed-link href="{{ route('order.products.create', ['order_id' => $instance->id]) }}">
            {{ __('Add product') }}
        </x-different.arrowed-link>
    @break

    @case('Manufacturer')
        {{ $instance->manufacturer->name }}
    @break

    @case('Country')
        {{ $instance->country->name }}
    @break

    @case('Currency')
        {{ $instance->currency->name }}
    @break

    @case('Readiness date')
        {{ $instance->readiness_date?->isoformat('DD MMM Y') }}
    @break

    @case('Mfg lead time')
        {{ $instance->lead_time }}
    @break

    @case('Expected dispatch date')
        {{ $instance->expected_dispatch_date?->isoformat('DD MMM Y') }}
    @break

    @case('Confirmed')
        @if ($instance->is_confirmed)
            <span class="badge badge--green">{{ __('Confirmed') }}</span>
        @else
            <span class="badge badge--grey">{{ __('Not confirmed') }}</span>
        @endif
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
