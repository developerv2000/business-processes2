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
                <th width="420" colspan="5">{{ __('Plan') }} {{ $plan->year }}</th>

                {{-- Quarters 1-4 --}}
                @for ($quarter = 1, $monthIndex = 0; $quarter <= 4; $quarter++)
                    <th width="270" colspan="3">{{ __('Quarter') }} {{ $quarter }}</th>

                    {{-- Monthes 1-12 --}}
                    @for ($quarterMonths = 1; $quarterMonths <= 3; $quarterMonths++)
                        <th width="330" colspan="4">{{ __($months[$monthIndex++]['name']) }}</th>
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

                {{-- Quarters 1 - 4 --}}
                @for ($quarter = 1; $quarter <= 4; $quarter++)
                    <th>Кк {{ __('plan') }}</th>
                    <th>Кк {{ __('fact') }}</th>
                    <th>НПР {{ __('fact') }}</th>

                    {{-- Monthes 1 - 12 --}}
                    @for ($quarterMonths = 1; $quarterMonths <= 3; $quarterMonths++)
                        {{-- January --}}
                        <th>Кк {{ __('plan') }}</th>
                        <th>Кк {{ __('fact') }}</th>
                        <th>НПР {{ __('fact') }}</th>
                        <th>{{ __('Comm') }}</th>
                    @endfor
                @endfor

            </tr>
        </thead> {{-- Head end --}}

        {{-- Body Start --}}
        <tbody>
            {{-- Summary row --}}
            <tr>
                <td class="plan-table__td--year"><strong>{{ $plan->year }}</strong></td>
                <td class="plan-table__td--mah-name"></td>

                {{-- Summary year --}}
                <td>{{ $plan->year_contract_plan }}</td>
                <td>{{ $plan->year_contract_fact }}</td>
                <td>{{ $plan->year_contract_fact_percentage }} %</td>
                <td>{{ $plan->year_register_fact }}</td>
                <td>{{ $plan->year_register_fact_percentage }} %</td>

                {{-- Summary Quarters 1 - 4 --}}
                @for ($quarter = 1, $monthIndex = 0; $quarter <= 4; $quarter++)
                    <td>{{ $plan->{'quarter_' . $quarter . '_contract_plan'} }}</td>
                    <td>{{ $plan->{'quarter_' . $quarter . '_contract_fact'} }}</td>
                    <td>{{ $plan->{'quarter_' . $quarter . '_register_fact'} }}</td>

                    {{-- Summary months 1 - 12 --}}
                    @for ($quarterMonths = 1; $quarterMonths <= 3; $quarterMonths++, $monthIndex++)
                        <td>{{ $plan->{$months[$monthIndex]['name'] . '_contract_plan'} }}</td>
                        <td>{{ $plan->{$months[$monthIndex]['name'] . '_contract_fact'} }}</td>
                        <td>{{ $plan->{$months[$monthIndex]['name'] . '_register_fact'} }}</td>
                        <td></td> {{-- Empty comment td --}}
                    @endfor
                @endfor
            </tr>

            {{-- Country code rows --}}
            @foreach ($plan->countryCodes as $country)
                <tr class="plan-table__divider"></tr> {{-- Empty space as divider --}}

                <tr>
                    <td class="plan-table__td--country-name"><strong>{{ $country->name }}</strong></td>
                    <td class="plan-table__td--mah-name"></td>

                    {{-- Country code year --}}
                    <td>{{ $country->year_contract_plan }}</td>
                    <td>{{ $country->year_contract_fact }}</td>
                    <td>{{ $country->year_contract_fact_percentage }} %</td>
                    <td>{{ $country->year_register_fact }}</td>
                    <td>{{ $country->year_register_fact_percentage }} %</td>

                    {{-- Country code Quarters 1 - 4 --}}
                    @for ($quarter = 1, $monthIndex = 0; $quarter <= 4; $quarter++)
                        <td>{{ $country->{'quarter_' . $quarter . '_contract_plan'} }}</td>
                        <td>{{ $country->{'quarter_' . $quarter . '_contract_fact'} }}</td>
                        <td>{{ $country->{'quarter_' . $quarter . '_register_fact'} }}</td>

                        {{-- Country code Months 1 - 12 --}}
                        @for ($quarterMonths = 1; $quarterMonths <= 3; $quarterMonths++, $monthIndex++)
                            <td>{{ $country->{$months[$monthIndex]['name'] . '_contract_plan'} }}</td>
                            <td>{{ $country->{$months[$monthIndex]['name'] . '_contract_fact'} }}</td>
                            <td>{{ $country->{$months[$monthIndex]['name'] . '_register_fact'} }}</td>
                            <td></td> {{-- Empty comment td --}}
                        @endfor
                    @endfor
                </tr>

                {{-- MAH rows --}}
                @foreach ($country->marketing_authorization_holders as $mah)
                    <tr>
                        <td class="plan-table__td--country-name">{{ $country->name }}</td>
                        <td class="plan-table__td--mah-name">{{ $mah->name }}</td>

                        {{-- MAH year --}}
                        <td>{{ $mah->year_contract_plan }}</td>
                        <td>{{ $mah->year_contract_fact }}</td>
                        <td>{{ $mah->year_contract_fact_percentage }} %</td>
                        <td>{{ $mah->year_register_fact }}</td>
                        <td>{{ $mah->year_register_fact_percentage }} %</td>

                        {{-- MAH Quarters 1 - 4 --}}
                        @for ($quarter = 1, $monthIndex = 0; $quarter <= 4; $quarter++)
                            <td>{{ $mah->{'quarter_' . $quarter . '_contract_plan'} }}</td>
                            <td>{{ $mah->{'quarter_' . $quarter . '_contract_fact'} }}</td>
                            <td>{{ $mah->{'quarter_' . $quarter . '_register_fact'} }}</td>

                            {{-- MAH Monthes 1 - 12 --}}
                            @for ($quarterMonths = 1; $quarterMonths <= 3; $quarterMonths++, $monthIndex++)
                                <td>{{ $mah->{$months[$monthIndex]['name'] . '_contract_plan'} }}</td>

                                <td>
                                    <a href="{{ $mah->{$months[$monthIndex]['name'] . '_contract_fact_link'} }}">
                                        {{ $mah->{$months[$monthIndex]['name'] . '_contract_fact'} }}
                                    </a>
                                </td>

                                <td>
                                    <a href="{{ $mah->{$months[$monthIndex]['name'] . '_register_fact_link'} }}">
                                        {{ $mah->{$months[$monthIndex]['name'] . '_register_fact'} }}
                                    </a>
                                </td>

                                <td>@include('tables.components.td.limited-text', ['text' => $mah->{$months[$monthIndex]['name'] . '_comment'}])</td>
                            @endfor
                        @endfor
                    </tr>
                @endforeach
            @endforeach
        </tbody> {{-- Body end --}}
    </table>
</div>
