@props([
    'name', // The name of the input field.
    'label', // The label text for the input field.
    'required' => $attributes->has('required'), // Indicates whether the input field is required.
    'errorName' => null, // Case bagged error names is used.
])

<x-forms.form-group label="{{ __($label) }}" error-name="{{ $errorName ?: $name }}" :required="$required">
    <input
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'input ' . (request()->has($name) ? 'input--highlight' : '')]) }}
        @if($required) required @endif
        value="{{ request()->input($name) }}"
    >
</x-forms.form-group>
