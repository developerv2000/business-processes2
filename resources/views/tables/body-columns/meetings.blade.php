@switch($column['name'])
    @case('Edit')
        @include('tables.components.td.edit-button', ['href' => route('meetings.edit', $instance->id)])
    @break

    @case('Year')
        {{ $instance->year }}
    @break

    @case('Manufacturer')
        {{ $instance->manufacturer->name }}
    @break

    @case('BDM')
        <x-different.ava image="{{ $instance->manufacturer->bdm->photo_asset_path }}" title="{{ $instance->manufacturer->bdm->name }}" />
    @break

    @case('Analyst')
        <x-different.ava image="{{ $instance->manufacturer->analyst->photo_asset_path }}" title="{{ $instance->manufacturer->analyst->name }}" />
    @break

    @case('Country')
        {{ $instance->manufacturer->country->name }}
    @break

    @case('Who met')
        @include('tables.components.td.limited-text', ['text' => $instance->who_met])
    @break

    @case('Plan')
        @include('tables.components.td.limited-text', ['text' => $instance->plan])
    @break

    @case('Topic')
        @include('tables.components.td.limited-text', ['text' => $instance->topic])
    @break

    @case('Result')
        @include('tables.components.td.limited-text', ['text' => $instance->result])
    @break

    @case('Outside the exhibition')
        @include('tables.components.td.limited-text', ['text' => $instance->outside_the_exhibition])
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
