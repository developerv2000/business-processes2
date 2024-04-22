@props(['action', 'id' => 'edit-form'])

<form {{ $attributes->merge(['class' => 'form edit-form']) }} action="{{ $action }}" id="{{ $id }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')

    {{ $slot }}
    <x-different.button class="form__submit" type="submit">{{ __('Update') }}</x-different.button>
</form>
