@props(['name', 'label', 'editing' => false, 'object' => null])

<x-form.groups.validateable label="{{ __($label) }}" error-name="{{ $name }}" required="{{ $attributes['required'] }}">
    <input name="{{ $name }}" {{ $attributes->merge(['class' => 'input']) }} value="{{ $editing ? old($name, $object->{$name}) : old($name) }}">
</x-form.groups.validateable>
