<style>
    .table-wrapper {
        --table-pagination-height: 0px;
    }
</style>

<div class="table-wrapper thin-scrollbar">
    <table class="table plan-table main-table">
        {{-- Head start --}}
        <thead>
            {{-- thead row 1 --}}
            <tr>
                <th class="plan-table__th--country-name">{{ __('Cтр') }}</th>
                <th class="plan-table__th--mah-name">{{ __('PC') }}</th>
                <th width="420" colspan="5">{{ __('Plan') }} {{ $request->year }}</th>

                {{-- Quoters 1-4 --}}
                @for ($quoter = 1, $monthIndex = 0; $quoter <= 4; $quoter++)
                    <th width="420" colspan="5">{{ __('Quoter') }} {{ $quoter }}</th>

                    {{-- Monthes 1-12 --}}
                    @for ($quoterMonths = 1; $quoterMonths <= 3; $quoterMonths++)
                        <th width="500" colspan="6">{{ __($months[$monthIndex++]['name']) }}</th>
                    @endfor
                @endfor
            </tr>

            {{-- thead row 2 --}}
            <tr>
                <th class="plan-table__th--country-name"></th>
                <th class="plan-table__th--mah-name"></th>

                {{-- Plan for the year --}}
                <th>Кк {{ __('plan') }}</th>
                <th>Кк {{ __('fact') }}</th>
                <th>Кк %</th>
                <th>НПР {{ __('fact') }}</th>
                <th>НПР %</th>

                {{-- Quoters 1 - 4 --}}
                @for ($quoter = 1; $quoter <= 4; $quoter++)
                    <th>Кк {{ __('plan') }}</th>
                    <th>Кк {{ __('fact') }}</th>
                    <th>Кк %</th>
                    <th>НПР {{ __('fact') }}</th>
                    <th>НПР %</th>

                    {{-- Monthes 1 - 12 --}}
                    @for ($quoterMonths = 1; $quoterMonths <= 3; $quoterMonths++)
                        {{-- January --}}
                        <th>Кк {{ __('plan') }}</th>
                        <th>Кк {{ __('fact') }}</th>
                        <th>Кк %</th>
                        <th>НПР {{ __('fact') }}</th>
                        <th>НПР %</th>
                        <th>{{ __('Comm') }}</th>
                    @endfor
                @endfor

            </tr>
        </thead> {{-- Head end --}}

        {{-- Body Start --}}
        <tbody>
            {{-- Plan row --}}
            <tr class="plan-table__td--country-name">
                <td class="plan-table__td--country-name plan-table__td--year"><strong>{{ $plan->year }}</strong></td>
                <td class="plan-table__td--mah-name"></td>

                {{-- Plan for the year --}}
                <td>{{ $plan->year_contract_plan }}</td>
                <td>{{ $plan->year_contract_fact }}</td>
                <td>{{ $plan->year_contract_fact_percentage }} %</td>
                <td>{{ $plan->year_register_fact }}</td>
                <td>{{ $plan->year_register_fact_percentage }} %</td>

                {{-- Plan quoters 1 - 4 --}}
                @for ($quoter = 1, $monthIndex = 0; $quoter <= 4; $quoter++)
                    <td>{{ $plan->{'quoter_' . $quoter . '_contract_plan'} }}</td>
                    <td>{{ $plan->{'quoter_' . $quoter . '_contract_fact'} }}</td>
                    <td>{{ $plan->{'quoter_' . $quoter . '_contract_fact_percentage'} }} %</td>
                    <td>{{ $plan->{'quoter_' . $quoter . '_register_fact'} }}</td>
                    <td>{{ $plan->{'quoter_' . $quoter . '_register_fact_percentage'} }} %</td>

                    {{-- Plan monthes 1 - 12 --}}
                    @for ($quoterMonths = 1; $quoterMonths <= 3; $quoterMonths++, $monthIndex++)
                        <td>{{ $plan->{$months[$monthIndex]['name'] . '_contract_plan'} }}</td>

                        <td>
                            {{ $plan->{$months[$monthIndex]['name'] . '_contract_fact'} }}
                        </td>

                        <td>{{ $plan->{$months[$monthIndex]['name'] . '_contract_fact_percentage'} }} %</td>

                        <td>
                            {{ $plan->{$months[$monthIndex]['name'] . '_register_fact'} }}
                        </td>

                        <td>{{ $plan->{$months[$monthIndex]['name'] . '_register_fact_percentage'} }} %</td>

                        <td></td> {{-- Comment --}}
                    @endfor
                @endfor
            </tr>

            {{-- Country code rows --}}
            @foreach ($plan->countryCodes as $country)
                <tr class="plan-table__divider"></tr> {{-- Empty space as divider --}}

                <tr class="plan-table__td--country-name">
                    <td class="plan-table__td--country-name"><strong>{{ $country->name }}</strong></td>
                    <td class="plan-table__td--mah-name"></td>

                    {{-- Country codes plan for the year --}}
                    <td>{{ $country->year_contract_plan }}</td>
                    <td>{{ $country->year_contract_fact }}</td>
                    <td>{{ $country->year_contract_fact_percentage }} %</td>
                    <td>{{ $country->year_register_fact }}</td>
                    <td>{{ $country->year_register_fact_percentage }} %</td>

                    {{-- Quoters 1 - 4 --}}
                    @for ($quoter = 1, $monthIndex = 0; $quoter <= 4; $quoter++)
                        <td>{{ $country->{'quoter_' . $quoter . '_contract_plan'} }}</td>
                        <td>{{ $country->{'quoter_' . $quoter . '_contract_fact'} }}</td>
                        <td>{{ $country->{'quoter_' . $quoter . '_contract_fact_percentage'} }} %</td>
                        <td>{{ $country->{'quoter_' . $quoter . '_register_fact'} }}</td>
                        <td>{{ $country->{'quoter_' . $quoter . '_register_fact_percentage'} }} %</td>

                        {{-- Monthes 1 - 12 --}}
                        @for ($quoterMonths = 1; $quoterMonths <= 3; $quoterMonths++, $monthIndex++)
                            <td>{{ $country->{$months[$monthIndex]['name'] . '_contract_plan'} }}</td>

                            <td>
                                {{ $country->{$months[$monthIndex]['name'] . '_contract_fact'} }}
                            </td>

                            <td>{{ $country->{$months[$monthIndex]['name'] . '_contract_fact_percentage'} }} %</td>

                            <td>
                                {{ $country->{$months[$monthIndex]['name'] . '_register_fact'} }}
                            </td>

                            <td>{{ $country->{$months[$monthIndex]['name'] . '_register_fact_percentage'} }} %</td>

                            <td></td> {{-- Comment --}}
                        @endfor
                    @endfor
                </tr>

                {{-- MAH rows --}}
                @foreach ($country->plan_marketing_authorization_holders as $mah)
                    <tr>
                        <td class="plan-table__td--country-name">{{ $country->name }}</td>
                        <td class="plan-table__td--mah-name">{{ $mah->name }}</td>

                        {{-- MAH plan for the year --}}
                        <td>{{ $mah->pivot->year_contract_plan }}</td>
                        <td>{{ $mah->pivot->year_contract_fact }}</td>
                        <td>{{ $mah->pivot->year_contract_fact_percentage }} %</td>
                        <td>{{ $mah->pivot->year_register_fact }}</td>
                        <td>{{ $mah->pivot->year_register_fact_percentage }} %</td>

                        {{-- Quoters 1 - 4 --}}
                        @for ($quoter = 1, $monthIndex = 0; $quoter <= 4; $quoter++)
                            <td>{{ $mah->pivot->{'quoter_' . $quoter . '_contract_plan'} }}</td>
                            <td>{{ $mah->pivot->{'quoter_' . $quoter . '_contract_fact'} }}</td>
                            <td>{{ $mah->pivot->{'quoter_' . $quoter . '_contract_fact_percentage'} }} %</td>
                            <td>{{ $mah->pivot->{'quoter_' . $quoter . '_register_fact'} }}</td>
                            <td>{{ $mah->pivot->{'quoter_' . $quoter . '_register_fact_percentage'} }} %</td>

                            {{-- Monthes 1 - 12 --}}
                            @for ($quoterMonths = 1; $quoterMonths <= 3; $quoterMonths++, $monthIndex++)
                                <td>{{ $mah->pivot->{$months[$monthIndex]['name'] . '_contract_plan'} }}</td>

                                <td>
                                    <a href="{{ $mah->pivot->{$months[$monthIndex]['name'] . '_contract_fact_link'} }}">
                                        {{ $mah->pivot->{$months[$monthIndex]['name'] . '_contract_fact'} }}
                                    </a>
                                </td>

                                <td>{{ $mah->pivot->{$months[$monthIndex]['name'] . '_contract_fact_percentage'} }} %</td>

                                <td>
                                    <a href="{{ $mah->pivot->{$months[$monthIndex]['name'] . '_register_fact_link'} }}">
                                        {{ $mah->pivot->{$months[$monthIndex]['name'] . '_register_fact'} }}
                                    </a>
                                </td>

                                <td>{{ $mah->pivot->{$months[$monthIndex]['name'] . '_register_fact_percentage'} }} %</td>
                                <td>@include('tables.components.td.limited-text', ['text' => $mah->pivot->{$months[$monthIndex]['name'] . '_comment'}])</td>
                            @endfor
                        @endfor
                    </tr>
                @endforeach
            @endforeach
        </tbody> {{-- Body end --}}
    </table>
</div>
