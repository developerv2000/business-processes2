@props([
    'label',        // Label text for the form field
    'errorName' => false,   // Error name for validation error handling, defaults to false if not provided
    'required' => false     // Flag indicating if the field is required, defaults to false
])

@php
    // Check if there's an error for this field
    $hasError = $errorName && $errors->has($errorName);
@endphp

<div class="form-group {{ $hasError ? 'form-group--error' : '' }}">
    <label class="label">
       {{-- Display the label text --}}
        <p class="label__text">
            {{ $label }}
           {{-- Display an asterisk (*) if the field is required --}}
            @if($required) <span class="label__required">*</span> @endif
        </p>

        <div class="form-group__input-container">
           {{-- Render the slot content (input field) --}}
            {{ $slot }}

           {{-- Display error icon if there's an error --}}
            @if($hasError)
                <span class="form-group__error-icon material-symbols-outlined">error</span>
            @endif
        </div>
    </label>

   {{-- Display error message if there's an error --}}
    @if($hasError)
        <p class="form-group__error-message">{{ $errors->first($errorName) }}</p>
    @endif
</div>
