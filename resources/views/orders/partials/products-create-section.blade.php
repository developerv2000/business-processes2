<div class="form__section">
    <div class="orders-edit__products-title-wrapper">
        <h2 class="main-title">{{ __('New product') }}</h2>
        <x-different.button class="orders-edit__delete-product" style="transparent" icon="close" type="button">{{ __('Delete') }}</x-different.button>
    </div>

    <x-forms.id-based-single-select.default-select
        label="Brand name ENG"
        name="process_id"
        :options="$processes"
        optionCaptionAttribute="fixed_trademark_en_for_order"
        required />

    <x-forms.id-based-single-select.default-select
        label="Country"
        name="country_code_id"
        :options="$countryCodes"
        required />

    <x-forms.id-based-single-select.default-select
        label="MAH"
        name="marketing_authorization_holder_id"
        :options="$marketingAuthorizationHolders"
        required />

    <x-forms.input.default-input
        label="Quantity"
        name="quantity"
        type="number" />

    <x-forms.input.default-input
        type="number"
        step="0.01"
        label="Price"
        name="price"
        required />
</div>
