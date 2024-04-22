@props([
    'name', // The name of the input field.
    'label', // The label text for the input field.
    'options', // Select options.
    'taggable' => false, // Whether user can and new options or not
    'required' => $attributes->has('required'), // Indicates whether the input field is required.
    'errorName' => null, // Case bagged error names is used.
])

<x-forms.groups.default-group label="{{ __($label) }}" error-name="{{ $errorName ?: $name }}" :required="$required">
    <select
        multiple
        name="{{ $name }}"
        {{ $attributes->merge(['class' => ($taggable ? 'multiple-taggable-selectize': 'multiple-selectize')]) }}
        @if($required) required @endif
    >
        @foreach ($options as $option)
            <option value="{{ $option }}">{{ $option }}</option>
        @endforeach
    </select>
</x-forms.groups.default-group>
