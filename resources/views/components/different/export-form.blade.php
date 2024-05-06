@props(['action'])

<form class="export-form" action="{{ $action }}" method="POST">
    @csrf

    <x-different.button style="action" icon="download" type="submit">{{ __('Export') }}</x-different.button>
</form>
