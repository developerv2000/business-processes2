@switch($column['name'])
    @case('Edit')
        @include('tables.components.td.edit-button', ['href' => route('orders.edit', $instance->id)])
    @break

    @case('Receive date')
        {{ $instance->receive_date->isoformat('DD MMM Y') }}
    @break

    @case('PO date')
        {{ $instance->purchase_order_date->isoformat('DD MMM Y') }}
    @break

    @case('Manufacturer')
        {{ $instance->manufacturer->name }}
    @break

    @case('Readiness date')
        {{ $instance->purchase_order_date->isoformat('DD MMM Y') }}
    @break

    @case('Mfg lead time')
        {{ $instance->purchase_order_date->isoformat('DD MMM Y') }}
    @break

    @case('Expected dispatch date')
        {{ $instance->purchase_order_date->isoformat('DD MMM Y') }}
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
