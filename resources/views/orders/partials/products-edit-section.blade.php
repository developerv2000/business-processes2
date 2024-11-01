<div class="form__section">
    <div class="orders-edit__products-title-wrapper">
        <h2 class="main-title">{{ __('Product') . ' #' . $product->id }}</h2>
        <x-different.button class="orders-edit__delete-product" style="transparent" icon="close" type="button">{{ __('Delete') }}</x-different.button>
    </div>

    <x-forms.id-based-single-select.instance-edit-select
        label="Brand name ENG"
        name="process_id"
        :options="$processes"
        optionCaptionAttribute="fixed_trademark_en_for_order"
        :instance="$product"
        required />

    <x-forms.id-based-single-select.instance-edit-select
        label="Country"
        name="country_code_id"
        :options="$countryCodes"
        :instance="$product"
        required />

    <x-forms.id-based-single-select.instance-edit-select
        label="MAH"
        name="marketing_authorization_holder_id"
        :options="$marketingAuthorizationHolders"
        :instance="$product"
        required />

    <x-forms.input.instance-edit-input
        label="Quantity"
        name="quantity"
        :instance="$product"
        type="number" />

    <x-forms.input.instance-edit-input
        type="number"
        step="0.01"
        label="Price"
        name="price"
        :instance="$product"
        required />
</div>
