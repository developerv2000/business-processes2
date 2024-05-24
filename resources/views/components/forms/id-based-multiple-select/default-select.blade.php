@props([
    'name', // The name of the input field.
    'label', // The label text for the input field.
    'options', // Select options.
    'optionCaptionAttribute' => 'name', // Attribute of options to display as captions.
    'required' => $attributes->has('required'), // Indicates whether the input field is required.
    'errorName' => null, // Case bagged error names is used.
    'defaultValues' => [], // The default values to be selected in the dropdown.
])

@php
    // Generate currently selected values, using old input data or the default values.
    $selectValues = old(rtrim($name, '[]'), $defaultValues);
@endphp

<x-forms.groups.default-group label="{{ __($label) }}" error-name="{{ $errorName ?: $name }}" :required="$required">
    <select
        multiple
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'multiple-selectize']) }}
        @if ($required) required @endif>

        @foreach ($options as $option)
            <option @selected(in_array($option->id, $selectValues)) value="{{ $option->id }}">{{ $option->{$optionCaptionAttribute} }}</option>
        @endforeach
    </select>
</x-forms.groups.default-group>
