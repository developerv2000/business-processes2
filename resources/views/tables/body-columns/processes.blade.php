@switch($column['name'])
    @case('Edit')
        @include('tables.components.td.edit-button', ['href' => route('processes.edit', $instance->id)])
    @break

    @case('ID')
        {{ $instance->id }}
    @break

@endswitch
