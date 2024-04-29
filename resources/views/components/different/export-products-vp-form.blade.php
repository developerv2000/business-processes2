@props(['manufacturer_id'])

<form class="export-form products-export-vp-form" action="{{ route('products.export-vp') }}" method="POST">
    @csrf
    <input type="hidden" name="manufacturer_id" value="{{ $manufacturer_id }}">

    <button class="button button--action">
        <span class="button__icon material-symbols-outlined">download</span>
        <span class="button__text">{{ __('VP') }}</span>
    </button>
</form>
