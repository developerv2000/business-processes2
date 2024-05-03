@switch($column['name'])
    @case('Edit')
        @include('tables.components.td.edit-button', ['href' => route('kvpp.edit', $instance->id)])
    @break

    @case('ID')
        {{ $instance->id }}
    @break

@endswitch
