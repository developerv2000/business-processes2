<style>
    .table-wrapper {
        --table-pagination-height: 0px;
    }
</style>

<div class="table-wrapper thin-scrollbar">
    <table class="table main-table">
        {{-- Head start --}}
        <thead>
            <tr>
                @include('tables.components.th.select-all')

                <th width="54">
                    @include('tables.components.th.edit')
                </th>

                <th width="112">{{ __('Name') }}</th>

                @foreach ($calendarMonths as $month)
                    <th width="120">{{ __($month['name']) . ' EU Кк' }}</th>
                    <th width="120">{{ __($month['name']) . ' IN Кк' }}</th>
                    <th width="200">{{ __($month['name']) . ' ' . __('Comment') }}</th>
                @endforeach
            </tr>
        </thead> {{-- Head end --}}

        {{-- Body Start --}}
        <tbody>
            @foreach ($records as $instance)
                <tr>
                    @include('tables.components.td.checkbox')

                    <td>
                        @include('tables.components.td.edit-button', [
                            'href' => route('plan.marketing.authorization.holders.edit', [
                                'plan' => $plan->id,
                                'countryCode' => $countryCode->id,
                                'marketingAuthorizationHolder' => $instance->id,
                            ]),
                        ])
                    </td>

                    <td>{{ $instance->name }}</td>

                    @foreach ($calendarMonths as $month)
                        <td>{{ $instance->pivot[$month['name'] . '_europe_contract_plan'] }}</td>
                        <td>{{ $instance->pivot[$month['name'] . '_india_contract_plan'] }}</td>

                        <td>
                            @include('tables.components.td.limited-text', ['text' => $instance->pivot[$month['name'] . '_comment']])
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody> {{-- Body end --}}
    </table>
</div>
