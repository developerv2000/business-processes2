@props(['action', 'id' => 'create-form', 'submitText' => 'Store'])

<form {{ $attributes->merge(['class' => 'form create-form']) }} action="{{ $action }}" id="{{ $id }}" method="POST" enctype="multipart/form-data" data-on-submit="show-spinner">
    @csrf
    <input type="hidden" name="previous_url" value="{{ old('previous_url', url()->previous()) }}">

    {{ $slot }}
    <x-different.button class="form__submit" type="submit">{{ __($submitText) }}</x-different.button>
</form>
