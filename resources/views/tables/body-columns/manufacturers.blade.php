@switch($column['name'])
    @case('Edit')
        @include('tables.components.td.edit-button', ['href' => route('manufacturers.edit', $instance->id)])
    @break

    @case('BDM')
        <x-different.ava image="{{ $instance->bdm->photo_asset_path }}" title="{{ $instance->bdm->name }}" />
    @break

    @case('Analyst')
        <x-different.ava image="{{ $instance->analyst->photo_asset_path }}" title="{{ $instance->analyst->name }}" />
    @break

    @case('Country')
        {{ $instance->country->name }}
    @break

    @case('IVP')
        <a class="td__link" href="#">{{ $instance->products_count }} {{ __('products') }}</a>
    @break

    @case('Manufacturer')
        {{ $instance->name }}
    @break

    @case('Category')
        <span @class([
            'badge',
            'badge--yellow' => $instance->category->name == 'УДС',
            'badge--pink' => $instance->category->name == 'НПП',
        ])>
            {{ $instance->category->name }}
        </span>
    @break

    @case('Status')
        @if ($instance->is_active)
            <span class="badge badge--blue">{{ __('Active') }}</span>
        @else
            <span class="badge badge--grey">{{ __('Stoped') }}</span>
        @endif
    @break

    @case('Important')
        @if ($instance->is_important)
            <span class="badge badge--orange">{{ __('Important') }}</span>
        @endif
    @break

    @case('Product class')
        <div class="td__badges">
            @foreach ($instance->productClasses as $class)
                <span class="badge badge--green">{{ $class->name }}</span>
            @endforeach
        </div>
    @break

    @case('Zones')
        @foreach ($instance->zones as $zone)
            {{ $zone->name }}<br>
        @endforeach
    @break

    @case('Black list')
        @foreach ($instance->blacklists as $list)
            {{ $list->name }}<br>
        @endforeach
    @break

    @case('Presence')
        <div class="td__limited-text" data-on-click="toggle-text-limit">
            @foreach ($instance->presences as $presence)
                {{ $presence->name }}<br>
            @endforeach
        </div>
    @break

    @case('Website')
        @include('tables.components.td.limited-text', ['text' => $instance->website])
    @break

    @case('About company')
        @include('tables.components.td.limited-text', ['text' => $instance->about])
    @break

    @case('Relationship')
        @include('tables.components.td.limited-text', ['text' => $instance->relationship])
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

    @case('Meetings')
        <x-different.arrowed-link href="#">{{ __('Meetings') }}</x-different.arrowed-link>
    @break

    @case('ID')
        {{ $instance->id }}
    @break

@endswitch
