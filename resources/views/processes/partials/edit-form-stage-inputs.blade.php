<input type="hidden" name="duplicating" value="{{ $duplicating }}"> {{-- Also used on duplication page --}}

{{-- Stage 1 (ВП) inputs --}}
@if ($stage >= 1)
    <div class="form__section">
        {{-- Search country can be edited on duplication at any stages --}}
        @if ($stage == 1 || $duplicating)
            <x-forms.id-based-single-select.instance-edit-select
                label="Search country"
                name="country_code_id"
                :options="$countryCodes"
                :instance="$instance"
                required />
        @else
            <x-forms.input.default-input
                label="Search country"
                :value="$instance->searchCountry->name"
                name="readonly"
                readonly />
        @endif

        <x-forms.id-based-multiple-select.instance-edit-select
            label="Responsible"
            name="responsiblePeople[]"
            :options="$responsiblePeople"
            :instance="$instance"
            required />

        @if ($stage <= 2)
            <x-forms.id-based-single-select.instance-edit-select
                label="Product class"
                name="class_id"
                :options="$productClasses"
                :instance="$product"
                required />
        @endif
    </div>
@endif

{{-- Stage 2 (ПО) inputs --}}
@if ($stage >= 2)
    <div class="form__section">
        <x-forms.input.instance-edit-input
            type="number"
            label="Forecast 1 year"
            name="forecast_year_1"
            :instance="$instance"
            required />

        <x-forms.input.instance-edit-input
            type="number"
            label="Forecast 2 year"
            name="forecast_year_2"
            :instance="$instance"
            required />

        <x-forms.input.instance-edit-input
            type="number"
            label="Forecast 3 year"
            name="forecast_year_3"
            :instance="$instance"
            required />
    </div>

    <div class="form__section">
        <x-forms.id-based-single-select.instance-edit-select
            label="Shelf life"
            name="shelf_life_id"
            :options="$shelfLifes"
            :instance="$product"
            required />

        <x-forms.input.instance-edit-input
            label="MOQ"
            name="moq"
            :instance="$product"
            required />

        <x-forms.input.instance-edit-input
            label="Dossier status"
            name="dossier_status"
            :instance="$instance" />

        <x-forms.input.instance-edit-input
            label="Year Cr/Be"
            name="clinical_trial_year"
            :instance="$instance" />

        <x-forms.id-based-multiple-select.instance-edit-select
            label="Countries Cr/Be"
            name="clinicalTrialCountries[]"
            :options="$countries"
            :instance="$instance" />

        <x-forms.input.instance-edit-input
            label="Country ich"
            name="clinical_trial_ich_country"
            :instance="$instance" />

        <x-forms.input.instance-edit-input
            label="Down payment 1"
            name="down_payment_1"
            :instance="$instance" />

        <x-forms.input.instance-edit-input
            label="Down payment 2"
            name="down_payment_2"
            :instance="$instance" />

        <x-forms.input.instance-edit-input
            label="Down payment condition"
            name="down_payment_condition"
            :instance="$instance" />
    </div>
@endif

{{-- Stage 3 (АЦ) inputs --}}
@if ($stage >= 3)
    <div class="form__section">
        {{-- Readonly when Manufacturer price 2 is not null --}}
        @if ($instance->manufacturer_followed_offered_price && !$duplicating)
            <x-forms.input.default-input
                label="Manufacturer price 1"
                :value="$instance->manufacturer_first_offered_price"
                name="readonly"
                readonly />
        @else
            {{-- Else required --}}
            <x-forms.input.instance-edit-input
                type="number"
                step="0.01"
                label="Manufacturer price 1"
                name="manufacturer_first_offered_price"
                :instance="$instance"
                required />
        @endif

        {{-- Required when its already not null --}}
        @if ($instance->manufacturer_followed_offered_price && !$duplicating)
            <x-forms.input.instance-edit-input
                type="number"
                step="0.01"
                label="Manufacturer price 2"
                name="manufacturer_followed_offered_price"
                :instance="$instance"
                required />
            {{-- Else nullable --}}
        @else
            <x-forms.input.instance-edit-input
                type="number"
                step="0.01"
                label="Manufacturer price 2"
                name="manufacturer_followed_offered_price"
                :instance="$instance" />
        @endif

        <x-forms.id-based-single-select.instance-edit-select
            label="Currency"
            name="currency_id"
            :options="$currencies"
            :instance="$instance"
            required />

        {{-- Readonly when Our price 2 is not null --}}
        @if ($instance->our_followed_offered_price && !$duplicating)
            <x-forms.input.default-input
                label="Our price 1"
                :value="$instance->our_first_offered_price"
                name="readonly"
                readonly />
        @else
            {{-- Else required --}}
            <x-forms.input.instance-edit-input
                type="number"
                step="0.01"
                label="Our price 1"
                name="our_first_offered_price"
                :instance="$instance"
                required />
        @endif

        {{-- Required when its already not null --}}
        @if ($instance->our_followed_offered_price && !$duplicating)
            <x-forms.input.instance-edit-input
                type="number"
                step="0.01"
                label="Our price 2"
                name="our_followed_offered_price"
                :instance="$instance"
                required />
            {{-- Else nullable --}}
        @else
            <x-forms.input.instance-edit-input
                type="number"
                step="0.01"
                label="Our price 2"
                name="our_followed_offered_price"
                :instance="$instance" />
        @endif

        {{-- These fields are nullable until stage 5 --}}
        @if ($stage < 5)
            <x-forms.id-based-single-select.instance-edit-select
                label="MAH"
                name="marketing_authorization_holder_id"
                :options="$marketingAuthorizationHolders"
                :instance="$instance" />

            <x-forms.input.instance-edit-input
                label="Brand Eng"
                name="trademark_en"
                :instance="$instance" />

            <x-forms.input.instance-edit-input
                label="Brand Rus"
                name="trademark_ru"
                :instance="$instance" />
        @else
            {{-- Else these fields became required from stage 5 --}}
            <x-forms.id-based-single-select.instance-edit-select
                label="MAH"
                name="marketing_authorization_holder_id"
                :options="$marketingAuthorizationHolders"
                :instance="$instance"
                required />

            <x-forms.input.instance-edit-input
                label="Brand Eng"
                name="trademark_en"
                :instance="$instance"
                required />

            <x-forms.input.instance-edit-input
                label="Brand Rus"
                name="trademark_ru"
                :instance="$instance"
                required />
        @endif
    </div>
@endif

{{-- Stage 4 (СЦ) inputs --}}
@if ($stage >= 4)
    <div class="form__section">
        <x-forms.input.instance-edit-input
            type="number"
            step="0.01"
            label="Agreed price"
            name="agreed_price"
            :instance="$instance"
            required />

        <x-forms.input.instance-edit-input
            type="number"
            step="0.01"
            label="Increased price"
            name="increased_price"
            :instance="$instance" />
    </div>
@endif
