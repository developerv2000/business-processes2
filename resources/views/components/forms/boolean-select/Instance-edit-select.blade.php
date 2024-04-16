@props([
    'name', // The name of the input field.
    'label',
    'trueOptionLabel' => 'Yes', // The label text for the true option.
    'falseOptionLabel' => 'No', // The label text for the false option.
    'trueOptionValue' => 1,
    'falseOptionValue' => 0,
    'instance', // The model instance for pre-selecting an option.
    'required' => $attributes->has('required'), // Indicates whether the input field is required.
    'errorName' => null, // Case bagged error names is used.
])

<x-forms.form-group label="{{ __($label) }}" error-name="{{ $errorName ?: $name }}" :required="$required">
    <select
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'singular-selectize']) }}
        @if($required) required @endif
    >
        @unless ($required)
            <option></option> {{-- Add an empty option for placeholder. --}}
        @endunless

        <option value="{{ $trueOptionValue }}" @selected($instance->{$name})>{{ __($trueOptionLabel) }}</option>
        <option value="{{ $falseOptionValue }}" @selected(!$instance->{$name})>{{ __($falseOptionLabel) }}</option>
    </select>
</x-forms.form-group>
