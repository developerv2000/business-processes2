@switch($column['name'])
    @case('Edit')
        @include('tables.components.td.edit-button', ['href' => route('order.products.edit', $instance->id)])
    @break

    @case('Receive date')
        {{ $instance->order->receive_date?->isoformat('DD MMM Y') }}
    @break

    @case('PO date')
        {{ $instance->order->purchase_order_date?->isoformat('DD MMM Y') }}
    @break

    @case('PO №')
        {{ $instance->order->purchase_order_name }}
    @break

    @case('Manufacturer')
        {{ $instance->order->manufacturer->name }}
    @break

    @case('Country')
        {{ $instance->country->name }}
    @break

    @case('Brand name ENG')
        {{ $instance->process->fixed_trademark_en_for_order }}
    @break

    @case('Brand name RUS')
        {{ $instance->process->fixed_trademark_ru_for_order }}
    @break

    @case('MAH')
        {{ $instance->marketingAuthorizationHolder->name }}
    @break

    @case('Quantity')
        {{ $instance->quantity }}
    @break

    @case('Price')
        {{ $instance->price }}
    @break

    @case('Currency')
        {{ $instance->order->currency->name }}
    @break

    @case('Sum')
        {{ $instance->total_price }}
    @break

    @case('Readiness date')
        {{ $instance->order->readiness_date?->isoformat('DD MMM Y') }}
    @break

    @case('Mfg lead time')
        {{ $instance->order->lead_time }}
    @break

    @case('Expected dispatch date')
        {{ $instance->order->expected_dispatch_date?->isoformat('DD MMM Y') }}
    @break

    @case('Confirmed')
        @if ($instance->order->is_confirmed)
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
