@props([
    'name', // The name of the input field.
    'label', // The label text for the input field.
    'required' => $attributes->has('required'), // Indicates whether the input field is required.
    'errorName' => null, // Case bagged error names is used.
    'rows' => 5 // rows of the input field
])

<x-forms.groups.default-group label="{{ __($label) }}" error-name="{{ $errorName ?: $name }}" :required="$required">
    <textarea
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'textarea ' . (request()->has($name) ? 'textarea--highlight' : '')]) }}
        rows={{ $rows }}
        @if ($required) required @endif>{{ request()->input($name) }}</textarea>

</x-forms.groups.default-group>
