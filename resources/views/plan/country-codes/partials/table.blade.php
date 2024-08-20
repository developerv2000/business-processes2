<table class="table main-table">
    {{-- Head start --}}
    <thead>
        <tr>
            <th width="54">
                @include('tables.components.th.edit')
            </th>

            <th>{{ __('Name') }}</th>
            <th>{{ __('MAH') }}</th>
            <th>{{ __('Edit') . ' ' . __('MAH') }}</th>
            <th>{{ __('Comments') }}</th>
        </tr>
    </thead> {{-- Head end --}}

    {{-- Body Start --}}
    <tbody>
        @foreach ($records as $instance)
            <tr>
                <td>
                    @include('tables.components.td.edit-button', [
                        'href' => route('plan.country.codes.edit', ['plan' => $plan->id, 'countryCode' => $instance->id]),
                    ])
                </td>

                <td>{{ $instance->name }}</td>

                <td>
                    @foreach ($instance->plan_marketing_authorization_holders as $mah)
                        {{ $mah->name }}
                    @endforeach
                </td>

                <td></td>

                <td>{{ $instance->comment }}</td>
            </tr>
        @endforeach
    </tbody> {{-- Body end --}}
</table>
