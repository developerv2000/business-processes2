<div class="form__section form__section--horizontal">
    <x-forms.id-based-single-select.default-select
        label="Brand name ENG"
        name="{{ 'new_products[' . $productIndex . '][process_id]' }}"
        :options="$processes"
        optionCaptionAttribute="fixed_trademark_en_for_order"
        required />

    <x-forms.id-based-single-select.default-select
        label="MAH"
        name="{{ 'new_products[' . $productIndex . '][marketing_authorization_holder_id]' }}"
        :options="$marketingAuthorizationHolders"
        required />

    <x-forms.input.default-input
        label="Quantity"
        name="{{ 'new_products[' . $productIndex . '][quantity]' }}"
        type="number" />

    <x-forms.input.default-input
        type="number"
        step="0.01"
        label="Price"
        name="{{ 'new_products[' . $productIndex . '][price]' }}"
        required />

    <span class="material-symbols-outlined orders-create__delete-product-btn">close</span>
</div>
