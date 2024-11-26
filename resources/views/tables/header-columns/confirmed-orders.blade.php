@switch($column['name'])
    {{-- Edit --}}
    @case('Edit')
        @include('tables.components.th.edit')
    @break
    
    @default
        @include('tables.components.th.unlinked-title')
    @break
@endswitch
