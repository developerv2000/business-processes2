@props([
    'name', // The name of the input field.
    'label', // The label text for the input field.
    'options', // Select options.
    'requestAttribute' => str_replace('[]', '', $name), // Request attribute may vary from real select name
    'taggable' => false, // Whether user can and new options or not
    'required' => $attributes->has('required'), // Indicates whether the input field is required.
    'errorName' => null, // Case bagged error names is used.
])

<x-forms.groups.default-group label="{{ __($label) }}" error-name="{{ $errorName ?: $name }}" :required="$required">
    <select
        multiple
        name="{{ $name }}"
        {{ $attributes->merge(['class' => ($taggable ? ' multiple-taggable-selectize': 'multiple-selectize') . (isset(request()->$requestAttribute) ? ' multiple-selectize--highlight' : '') ]) }}
        @if($required) required @endif
    >
        @foreach ($options as $option)
            <option
                value="{{ $option }}"
                @selected(request()->{$requestAttribute} && in_array($option, request()->{$requestAttribute}))
            >
                {{ $option }}
            </option>
        @endforeach
    </select>
</x-forms.groups.default-group>
