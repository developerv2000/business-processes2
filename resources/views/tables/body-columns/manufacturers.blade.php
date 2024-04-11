@switch($column['name'])
    @case('Edit')
        @include('tables.components.td.edit', ['href' => route('manufacturers.edit', $item->id)])
    @break

    @case('BDM')
        <x-different.ava image="{{ $item->bdm->photo_assets_path }}" text="{{ $item->bdm->name }}" />
    @break

    @case('Analyst')
        <x-different.ava image="{{ $item->analyst->photo_assets_path }}" text="{{ $item->analyst->name }}" />
    @break

    @case('Country')
        {{ $item->country->name }}
    @break

    @case('IVP')
        <a class="td__link" href="#">10 {{ __('products') }}</a>
    @break

    @case('Manufacturer')
        {{ $item->name }}
    @break

    @case('Category')
        <span @class([
            'badge',
            'badge--yellow' => $item->category->name == 'УДС',
            'badge--pink' => $item->category->name == 'НПП',
        ])>
            {{ $item->category->name }}
        </span>
    @break

    @case('Status')
        @if ($item->is_active)
            <span class="badge badge--blue">{{ __('Active') }}</span>
        @else
            <span class="badge badge--grey">{{ __('Stoped') }}</span>
        @endif
    @break

    @case('Important')
        @if ($item->is_important)
            <span class="badge badge--orange">{{ __('Important') }}</span>
        @endif
    @break

    @case('Product category')
        <div class="td__badges">
            @foreach ($item->productClasses as $class)
                <span class="badge badge--green">{{ $class->name }}</span>
            @endforeach
        </div>
    @break

    @case('Zones')
        @foreach ($item->zones as $zone)
            {{ $zone->name }}<br>
        @endforeach
    @break

    @case('Black list')
        @foreach ($item->blacklists as $list)
            {{ $list->name }}<br>
        @endforeach
    @break

    @case('Presence')
        <div class="td__limited-text" data-on-click="toggle-text-limit">
            @foreach ($item->presences as $presence)
                {{ $presence->name }}<br>
            @endforeach
        </div>
    @break

    @case('Website')
        @include('tables.components.td.limited-text', ['text' => $item->website])
    @break

    @case('About company')
        @include('tables.components.td.limited-text', ['text' => $item->about])
    @break

    @case('Relationship')
        @include('tables.components.td.limited-text', ['text' => $item->relationship])
    @break

    @case('Comments')
        <x-different.arrowed-link href="#">{{ __('View') }}</x-different.arrowed-link>
    @break

    @case('Last comment')
        @include('tables.components.td.limited-text', ['text' => $item->lastComment?->body])
    @break

    @case('Comments date')
        {{ $item->lastComment?->created_at->isoformat('DD MMM Y') }}
    @break

    @case('Date of creation')
        {{ $item->created_at->isoformat('DD MMM Y') }}
    @break

    @case('Update date')
        {{ $item->updated_at->isoformat('DD MMM Y') }}
    @break

    @case('Meetings')
        <x-different.arrowed-link href="#">{{ __('Meetings') }}</x-different.arrowed-link>
    @break

    @case('ID')
        {{ $item->id }}
    @break

@endswitch
