@props(['action', 'id' => 'create-form'])

<form {{ $attributes->merge(['class' => 'form create-form']) }} action="{{ $action }}" id="{{ $id }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{ $slot }}
    <x-different.button class="form__submit" type="submit">{{ __('Store') }}</x-different.button>
</form>
