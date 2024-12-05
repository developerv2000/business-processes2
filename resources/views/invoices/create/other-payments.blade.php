<div class="form__section">
    <div class="form__row">
        <x-forms.input.default-input
            label="Name"
            name="{{ 'other_payments[' . $paymentIndex . '][name]' }}"
            required />

        <x-forms.input.default-input
            label="Quantity"
            name="{{ 'other_payments[' . $paymentIndex . '][quantity]' }}"
            type="number"
            required />

        <x-forms.input.default-input
            type="number"
            step="0.01"
            label="Price"
            name="{{ 'other_payments[' . $paymentIndex . '][price]' }}"
            required />

        <span class="material-symbols-outlined invoices-create__delete-other-payments-btn">close</span>
    </div>
</div>
