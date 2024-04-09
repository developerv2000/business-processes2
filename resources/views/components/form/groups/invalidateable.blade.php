@props(['label', 'required' => false])

<div {{ $attributes->merge(['class' => 'form-group']) }}>
    <label class="label">
        <p class="label__text">{{ $label }}@if($required)<x-form.required-symbol />@endif</p>

        <div class="form-group__input-container">
            {{ $slot }}
            <span class="form-group__error-icon material-symbols-outlined">error</span>
        </div>
    </label>
</div>
