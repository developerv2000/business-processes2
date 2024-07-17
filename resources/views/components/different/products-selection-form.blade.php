@props(['model'])

<form class="export-form products-selection-form" action="{{ route('products-selection.export') }}" method="POST">
    @csrf
    <input type="hidden" name="model" value="{{ $model }}">
    <x-different.button style="action" icon="download" type="submit">{{ __('VP') }}</x-different.button>
</form>
