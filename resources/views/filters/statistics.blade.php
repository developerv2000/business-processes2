@extends('filters.template')

@section('elements')
    <x-forms.input.request-based-input
        type="number"
        label="Year"
        name="year" />

    <x-forms.groups.default-group label="{{ __('Months') }}">
        <select multiple name="months[]" class="multiple-selectize @if ($request->has('months')) multiple-selectize--highlight @endif">
            @foreach ($calendarMonths as $month)
                <option value="{{ $month['number'] }}"
                    @selected($request->has('months') && in_array($month['number'], $request->months))>
                    {{ __($month['name']) }}
                </option>
            @endforeach
        </select>
    </x-forms.groups.default-group>

    <x-forms.id-based-single-select.request-based-select
        label="BDM"
        name="bdm_user_id"
        :options="$bdmUsers" />

    @if ($request->user()->isAdminOrModerator())
        <x-forms.id-based-single-select.request-based-select
            label="Analyst"
            name="analyst_user_id"
            :options="$analystUsers" />

        <x-forms.boolean-select.request-based-select
            label="Extensive statistics"
            name="extensive" />
    @endif

    <x-forms.id-based-single-select.request-based-select
        label="Search country"
        name="country_code_id"
        :options="$countryCodes" />

    <x-forms.single-select.request-based-select
        label="Manufacturer countries"
        name="specific_manufacturer_country"
        :options="$specificManufacturerCountries" />
@endsection
