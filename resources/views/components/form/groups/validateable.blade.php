@props(['label', 'errorName', 'required' => false])

<div class="form-group @error($errorName) form-group--error @enderror">
    <label class="label">
        <p class="label__text">{{ $label }}@if($required)<x-form.required-symbol />@endif</p>

        <div class="form-group__input-container">
            {{ $slot }}
            <span class="form-group__error-icon material-symbols-outlined">error</span>
        </div>
    </label>

    <p class="form-group__error-message">@error($errorName) {{ $message }} @enderror</p>
</div>
