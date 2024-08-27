<table class="table main-table">
    {{-- Head start --}}
    <thead>
        <tr>
            @include('tables.components.th.select-all')

            <th width="54">
                @include('tables.components.th.edit')
            </th>

            <th>{{ __('Name') }}</th>
            <th>{{ __('MAH') }}</th>
            <th>{{ __('MAH') }}</th>
        </tr>
    </thead> {{-- Head end --}}

    {{-- Body Start --}}
    <tbody>
        @foreach ($records as $instance)
            <tr>
                @include('tables.components.td.checkbox')

                <td>
                    @include('tables.components.td.edit-button', [
                        'href' => route('plan.country.codes.edit', ['plan' => $plan->id, 'countryCode' => $instance->id]),
                    ])
                </td>

                <td>{{ $instance->name }}</td>

                <td>
                    @foreach ($instance->plan_marketing_authorization_holders as $mah)
                        <a class="td__link"
                            href="{{ route('plan.marketing.authorization.holders.edit', [
                                'plan' => $plan->id,
                                'countryCode' => $instance->id,
                                'marketingAuthorizationHolder' => $mah->id,
                            ]) }}">
                            {{ $mah->name }}
                        </a>
                    @endforeach
                </td>

                <td>
                    <a class="td__link"
                        href="{{ route('plan.marketing.authorization.holders.index', [
                            'plan' => $plan->id,
                            'countryCode' => $instance->id,
                        ]) }}">{{ __('Edit') }}</a>
                </td>
            </tr>
        @endforeach
    </tbody> {{-- Body end --}}
</table>
