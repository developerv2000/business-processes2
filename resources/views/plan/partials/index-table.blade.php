<table class="table main-table">
    {{-- Head start --}}
    <thead>
        <tr>
            @include('tables.components.th.select-all')

            <th width="54">
                @include('tables.components.th.edit')
            </th>

            <th width="50">
                @include('tables.components.th.iconed-title', [
                    'title' => 'View',
                    'icon' => 'visibility',
                ])
            </th>

            <th>{{ __('Year') }}</th>

            <th>{{ __('Кк план') }}</th>
            <th>{{ __('Кк факт') }}</th>
            <th>{{ __('Кк %') }}</th>
            <th>{{ __('НПР факт') }}</th>
            <th>{{ __('НПР %') }}</th>

            <th>{{ __('Countries') }}</th>

            <th>{{ __('Comments') }}</th>
            <th width="240">{{ __('Last comment') }}</th>
        </tr>
    </thead> {{-- Head end --}}

    {{-- Body Start --}}
    <tbody>
        @foreach ($records as $instance)
            <tr>
                @include('tables.components.td.checkbox')

                <td>
                    @include('tables.components.td.edit-button', ['href' => route('plan.edit', $instance->id)])
                </td>

                <td>
                    <x-different.linked-button
                        style="transparent"
                        class="td__view"
                        href="{{ route('plan.show') }}?year={{ $instance->year }}"
                        icon="visibility"
                        title="{{ __('View') }}" />
                </td>

                <td>{{ $instance->year }}</td>

                <td>{{ $instance->year_contract_plan }}</td>
                <td>{{ $instance->year_contract_fact }}</td>
                <td>{{ $instance->year_contract_fact_percentage }} %</td>
                <td>{{ $instance->year_register_fact }}</td>
                <td>{{ $instance->year_register_fact_percentage }} %</td>

                <td><a class="td__link" href="{{ route('plan.country.codes.index', $instance->id) }}">{{ __('Countries') }}</a></td>

                <td>@include('tables.components.td.all-comments-link')</td>

                <td>@include('tables.components.td.limited-text', ['text' => $instance->lastComment?->body])</td>
            </tr>
        @endforeach
    </tbody> {{-- Body end --}}
</table>
