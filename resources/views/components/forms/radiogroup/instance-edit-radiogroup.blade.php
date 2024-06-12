@props([
    'name', // The name of the input field.
    'label', // The label text for the input field.
    'instance', // The model instance for pre-selecting an option.
    'options', // Select options.
    'optionCaptionAttribute' => 'caption', // Attribute of options to display as captions.
    'optionValueAttribute' => 'value', // Attribute of options to set as value.
    'required' => $attributes->has('required'), // Indicates whether the input field is required.
    'errorName' => null, // Case bagged error names is used.
])

<x-forms.groups.radio-group label="{{ __($label) }}" error-name="{{ $errorName ?: $name }}" :required="$required">
    @foreach ($options as $option)
        <label class="radio-group__option-label">
            <input
                class="radio"
                type="radio"
                name="{{ $name }}"
                value="{{ $option->{$optionValueAttribute} }}"
                @checked($option->{$optionValueAttribute} == $instance->{$name})
                @if ($required) required @endif>

            <div class="radio-group__option-caption">{{ $option->{$optionCaptionAttribute} }}</div>
        </label>
    @endforeach
</x-forms.groups.radio-group>
