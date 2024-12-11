<div class="form__section">
    <div class="form__row">
        <x-forms.input.default-input
            label="Description"
            name="{{ 'services[' . $servicetIndex . '][description]' }}"
            required />

        <x-forms.input.default-input
            class="invoices-create__quantity-input"
            label="Quantity"
            name="{{ 'services[' . $servicetIndex . '][quantity]' }}"
            type="number"
            required />

        <x-forms.input.default-input
            class="invoices-create__price-input"
            type="number"
            step="0.01"
            label="Price"
            name="{{ 'services[' . $servicetIndex . '][price]' }}"
            required />

        <x-forms.input.default-input
            class="invoices-create__total-price-input"
            label="Sum price"
            name="{{ 'services[' . $servicetIndex . '][sum_price]' }}"
            readonly />

        <span class="material-symbols-outlined invoices-create__delete-service-btn">close</span>
    </div>
</div>
