@switch($column['name'])
    @case('Edit')
        @include('tables.components.td.edit-button', ['href' => route('products.edit', $instance->id)])
    @break

    @case('Processes')
        <a class="td__link td__link--margined" href="{{ $instance->processes_index_filtered_link }}">
            {{ $instance->untrashed_processes_count }} {{ __('processes') }}
        </a>

        <x-different.arrowed-link href="{{ route('processes.create', ['product_id' => $instance->id]) }}">
            {{ __('New process') }}
        </x-different.arrowed-link>
    @break

    @case('Category')
        <span @class([
            'badge',
            'badge--yellow' => $instance->manufacturer->category->name == 'УДС',
            'badge--pink' => $instance->manufacturer->category->name == 'НПП',
        ])>
            {{ $instance->manufacturer->category->name }}
        </span>
    @break

    @case('Country')
        {{ $instance->manufacturer->country->name }}
    @break

    @case('Manufacturer')
        {{ $instance->manufacturer->name }}
    @break

    @case('Manufacturer Brand')
        {{ $instance->brand }}
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

    @case('MOQ')
        @include('tables.components.td.formatted-price', ['attribute' => 'moq'])
    @break

    @case('Shelf life')
        {{ $instance->shelfLife->name }}
    @break

    @case('Product class')
        <span class="badge badge--green">{{ $instance->class->name }}</span>
    @break

    @case('Dossier')
        @include('tables.components.td.limited-text', ['text' => $instance->dossier])
    @break

    @case('Zones')
        @foreach ($instance->zones as $zone)
            {{ $zone->name }}<br>
        @endforeach
    @break

    @case('Bioequivalence')
        @include('tables.components.td.limited-text', ['text' => $instance->bioequivalence])
    @break

    @case('Validity period')
        {{ $instance->validity_period }}
    @break

    @case('Registered in EU')
        @if ($instance->registered_in_eu)
            <span class="badge badge--orange">{{ __('Registered') }}</span>
        @endif
    @break

    @case('Sold in EU')
        @if ($instance->sold_in_eu)
            <span class="badge badge--blue">{{ __('Sold') }}</span>
        @endif
    @break

    @case('Down payment')
        {{ $instance->down_payment }}
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

    @case('BDM')
        <x-different.ava image="{{ $instance->manufacturer->bdm->photo_asset_path }}" title="{{ $instance->manufacturer->bdm->name }}" />
    @break

    @case('Analyst')
        <x-different.ava image="{{ $instance->manufacturer->analyst->photo_asset_path }}" title="{{ $instance->manufacturer->analyst->name }}" />
    @break

    @case('Date of creation')
        {{ $instance->created_at->isoformat('DD MMM Y') }}
    @break

    @case('Update date')
        {{ $instance->updated_at->isoformat('DD MMM Y') }}
    @break

    @case('KVPP coincidents')
        @foreach ($instance->coincident_kvpps as $coincidentKvpp)
            <a class="td__link" href="{{ route('kvpp.index', ['id[]' => $coincidentKvpp->id]) }}">
                # {{ $coincidentKvpp->id }} {{ $coincidentKvpp->country->name }}
            </a><br>
        @endforeach
    @break

    @case('ID')
        {{ $instance->id }}
    @break

@endswitch
