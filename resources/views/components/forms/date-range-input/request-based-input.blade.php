@props([
    'name', // The name of the input field.
    'label', // The label text for the input field.
    'required' => $attributes->has('required'), // Indicates whether the input field is required.
    'errorName' => null, // Case bagged error names is used.
])

<x-forms.groups.default-group label="{{ __($label) }}" error-name="{{ $errorName ?: $name }}" :required="$required">
    <input
        name="{{ $name }}"
        type="text"
        autocomplete="off"
        {{ $attributes->merge(['class' => 'input date-range-input ' . (request()->has($name) ? 'input--highlight' : '')]) }}
        @if($required) required @endif
        value="{{ request()->input($name) }}"
    >
</x-forms.groups.default-group>
