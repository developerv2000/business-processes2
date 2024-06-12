@props([
    'name', // The name of the input field.
    'label', // The label text for the input field.
    'instance', // The instance being edited.
    'required' => $attributes->has('required'), // Indicates whether the input field is required.
    'errorName' => null, // Case bagged error names is used.
])

<x-forms.groups.default-group label="{{ __($label) }}" error-name="{{ $errorName ?: $name }}" :required="$required">
    <input
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'input']) }}
        @if ($required) required @endif
        value="{{ old($name, $instance->{$name}) }}">
</x-forms.groups.default-group>
