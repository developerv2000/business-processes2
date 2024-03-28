@props(['name'])

<input name="{{ $name }}" value="{{ old($name) }}" {{ $attributes->merge(['class' => 'input']) }}>
