<div class="form__section">
    <div class="form__row">
        <x-forms.input.default-input
            label="Description"
            name="{{ 'other_payments[' . $paymentIndex . '][description]' }}"
            required />

        <x-forms.input.default-input
            class="invoices-create__quantity-input"
            label="Quantity"
            name="{{ 'other_payments[' . $paymentIndex . '][quantity]' }}"
            type="number"
            required />

        <x-forms.input.default-input
            class="invoices-create__price-input"
            type="number"
            step="0.01"
            label="Price"
            name="{{ 'other_payments[' . $paymentIndex . '][price]' }}"
            required />

        <x-forms.input.default-input
            class="invoices-create__total-price-input"
            label="Sum price"
            name="{{ 'other_payments[' . $paymentIndex . '][sum_price]' }}"
            readonly />

        <span class="material-symbols-outlined invoices-create__delete-other-payments-btn">close</span>
    </div>
</div>
