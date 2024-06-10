{{-- Default inputs for all stages --}}
<div class="form__section">
    <x-forms.id-based-single-select.instance-edit-select
        label="Product class"
        name="class_id"
        :options="$productClasses"
        :instance="$product"
        required />

    <x-forms.id-based-multiple-select.default-select
        label="Responsible"
        name="responsiblePeople[]"
        :options="$responsiblePeople"
        required />
</div>

{{-- Stage 2 (ПО) inputs --}}
@if ($stage >= 2)
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

        <x-forms.input.default-input
            label="Dossier status"
            name="dossier_status" />

        <x-forms.input.default-input
            label="Year Cr/Be"
            name="clinical_trial_year" />

        <x-forms.id-based-multiple-select.default-select
            label="Countries Cr/Be"
            name="clinicalTrialCountries[]"
            :options="$countries" />

        <x-forms.input.default-input
            label="Country ich"
            name="clinical_trial_ich_country" />

        <x-forms.input.default-input
            label="Down payment 1"
            name="down_payment_1" />

        <x-forms.input.default-input
            label="Down payment 2"
            name="down_payment_2" />

        <x-forms.input.default-input
            label="Down payment condition"
            name="down_payment_condition" />
    </div>
@endif

{{-- Stage 3 (АЦ) inputs --}}
@if ($stage >= 3)
    <div class="form__section">
        <x-forms.input.default-input
            type="number"
            step="0.01"
            label="Manufacturer price 1"
            name="manufacturer_first_offered_price"
            required />

        <x-forms.input.default-input
            type="number"
            step="0.01"
            label="Manufacturer price 2"
            name="manufacturer_followed_offered_price" />

        <x-forms.id-based-single-select.default-select
            label="Currency"
            name="currency_id"
            :options="$currencies"
            required />

        <x-forms.input.default-input
            type="number"
            step="0.01"
            label="Our price 1"
            name="our_first_offered_price"
            required />

        <x-forms.input.default-input
            type="number"
            step="0.01"
            label="Our price 2"
            name="our_followed_offered_price" />

        {{-- These fields are nullable until stage 5 --}}
        @if ($stage < 5)
            <x-forms.id-based-single-select.default-select
                label="MAH"
                name="marketing_authorization_holder_id"
                :options="$marketingAuthorizationHolders" />

            <x-forms.input.default-input
                label="Brand Eng"
                name="trademark_en" />

            <x-forms.input.default-input
                label="Brand Rus"
                name="trademark_ru" />
        @else
            {{-- Else these fields became required from stage 5 --}}
            <x-forms.id-based-single-select.default-select
                label="MAH"
                name="marketing_authorization_holder_id"
                :options="$marketingAuthorizationHolders"
                required />

            <x-forms.input.default-input
                label="Brand Eng"
                name="trademark_en"
                required />

            <x-forms.input.default-input
                label="Brand Rus"
                name="trademark_ru"
                required />
        @endif
    </div>
@endif

{{-- Stage 4 (СЦ) inputs --}}
@if ($stage >= 4)
    <div class="form__section">
        <x-forms.input.default-input
            type="number"
            step="0.01"
            label="Increased price"
            name="increased_price" />
    </div>
@endif


{{-- Stage 5 (КК) inputs --}}
@if ($stage >= 5)
    <div class="form__section">
        <x-forms.input.default-input
            type="number"
            step="0.01"
            label="Agreed price"
            name="agreed_price"
            required />
    </div>
@endif
