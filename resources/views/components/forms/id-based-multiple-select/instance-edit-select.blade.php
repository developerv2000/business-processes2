@props([
    'name', // The name of the input field.
    'label', // The label text for the input field.
    'options', // Select options.
    'optionCaptionAttribute' => 'name', // Attribute of options to display as captions.
    'instance', // The model instance for pre-selecting an option.
    'instanceRelationName' => str_replace('[]', '', $name), // Instance relation name may vary from real select name
    'required' => $attributes->has('required'), // Indicates whether the input field is required.
    'errorName' => null, // Case bagged error names is used.
])

<x-forms.groups.default-group label="{{ __($label) }}" error-name="{{ $errorName ?: $name }}" :required="$required">
    <select
        multiple
        name="{{ $name }}"
        {{ $attributes->merge(['class' => 'multiple-selectize']) }}
        @if($required) required @endif
    >
        @foreach ($options as $option)
            <option
                value="{{ $option->id }}"
                @selected($instance->{$instanceRelationName}->contains('id', $option->id))
            >
                {{ $option->{$optionCaptionAttribute} }}
            </option>
        @endforeach
    </select>
</x-forms.groups.default-group>
