@props(['action'])

<form class="export-form" action="{{ $action }}" method="POST">
    @csrf
    <input type="hidden" name="previous_url" value="{{ url()->full() }}">

    <button class="button button--action">
        <span class="button__icon material-symbols-outlined">download</span>
        <span class="button__text">{{ __('Export') }}</span>
    </button>
</form>
