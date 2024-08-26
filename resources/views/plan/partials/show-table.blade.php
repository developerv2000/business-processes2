<style>
    .table-wrapper {
        --table-pagination-height: 0px;
    }
</style>

<div class="table-wrapper plan-table-wrapper thin-scrollbar">
    <table class="table plan-table main-table">
        {{-- Head start --}}
        <thead>
            {{-- thead row 1 --}}
            <tr>
                <th width="110">{{ __('Country code') }}</th>
                <th width="76">{{ __('MAH') }}</th>
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
                <th></th>
                <th></th>

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
            @foreach ($plan->countryCodes as $country)
                <tr>
                    <td>{{ $country->name }}</td>
                </tr>

                @foreach ($country->plan_marketing_authorization_holders as $mah)
                    <tr>
                        <td>{{ $country->name }}</td>
                        <td>{{ $mah->name }}</td>

                        {{-- Plan for the year --}}
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>

                        {{-- Quoters 1 - 4 --}}
                        @for ($quoter = 1, $monthIndex = 0; $quoter <= 4; $quoter++)
                            <td>{{ $mah->pivot->{'quoter_' . $quoter . '_contract_plan'} }}</td>
                            <td>{{ $mah->pivot->{'quoter_' . $quoter . '_contract_fact'} }}</td>
                            <td>{{ $mah->pivot->{'quoter_' . $quoter . '_contract_percentage'} }}</td>
                            <td>{{ $mah->pivot->{'quoter_' . $quoter . '_register_fact'} }}</td>
                            <td>{{ $mah->pivot->{'quoter_' . $quoter . '_register_percentage'} }}</td>

                            {{-- Monthes 1 - 12 --}}
                            @for ($quoterMonths = 1; $quoterMonths <= 3; $quoterMonths++, $monthIndex++)
                                {{-- January --}}
                                <td>{{ $mah->pivot->{$months[$monthIndex]['name'] . '_contract_plan'} }}</td>

                                <td>
                                    <a href="{{ $mah->pivot->{$months[$monthIndex]['name'] . '_contract_fact_link'} }}">
                                        {{ $mah->pivot->{$months[$monthIndex]['name'] . '_contract_fact'} }}
                                    </a>
                                </td>

                                <td>{{ $mah->pivot->{$months[$monthIndex]['name'] . '_contract_percentage'} }}</td>

                                <td>
                                    <a href="{{ $mah->pivot->{$months[$monthIndex]['name'] . '_register_fact_link'} }}">
                                        {{ $mah->pivot->{$months[$monthIndex]['name'] . '_register_fact'} }}
                                    </a>
                                </td>

                                <td>{{ $mah->pivot->{$months[$monthIndex]['name'] . '_register_percentage'} }}</td>
                                <td>@include('tables.components.td.limited-text', ['text' => $mah->pivot->{$months[$monthIndex]['name'] . '_comment'}])</td>
                            @endfor
                        @endfor
                    </tr>
                @endforeach
            @endforeach
        </tbody> {{-- Body end --}}
    </table>
</div>
