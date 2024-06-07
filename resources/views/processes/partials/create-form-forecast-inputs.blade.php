{{-- Forecast inputs are required from Stage 2 (ПО) --}}
@if ($stage >= 2 && count($selectedCountryCodes))
    @foreach ($selectedCountryCodes as $country)
        <div class="form__section">
            <x-forms.input.default-input
                type="number"
                :label="__('Forecast 1 year') . ' ' . $country"
                :name="'forecast_year_1_' . $country"
                required />

            <x-forms.input.default-input
                type="number"
                :label="__('Forecast 2 year') . ' ' . $country"
                :name="'forecast_year_2_' . $country"
                required />

            <x-forms.input.default-input
                type="number"
                :label="__('Forecast 3 year') . ' ' . $country"
                :name="'forecast_year_3_' . $country"
                required />
        </div>
    @endforeach
@endif
