@props([
    'name', // The name of the input field.
    'label', // The label text for the input field.
    'options', // Select options.
    'optionCaptionAttribute' => 'name', // Attribute of options to display as captions.
    'required' => $attributes->has('required'), // Indicates whether the input field is required.
    'errorName' => null, // Case bagged error names is used.
])

<x-forms.groups.default-group label="{{ __($label) }}" error-name="{{ $errorName ?: $name }}" :required="$required">
    <select
        name="{{ $name }}"
        {{ $attributes->merge(['class' => request()->input($name) ? 'singular-selectize singular-selectize--highlight' : 'singular-selectize']) }}
        @if($required) required @endif
    >
        @unless ($required)
            <option></option> {{-- Add an empty option for placeholder. --}}
        @endunless

        @foreach ($options as $option)
            <option
                value="{{ $option->id }}"
                @selected($option->id == request()->input($name))
            >
                {{ $option->{$optionCaptionAttribute} }}
            </option>
        @endforeach
    </select>
</x-forms.groups.default-group>
