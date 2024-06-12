@props(['manufacturer_id'])

<form class="export-form products-export-vp-form" action="{{ route('products.export-vp') }}" method="POST">
    @csrf
    <input type="hidden" name="manufacturer_id" value="{{ $manufacturerId }}">
    <x-different.button style="action" icon="download" type="submit">{{ __('VP') }}</x-different.button>
</form>
