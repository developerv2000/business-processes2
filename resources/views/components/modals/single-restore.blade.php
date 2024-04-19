@props(['action'])

<x-modals.template class="single-restore-modal" title="{{ __('Restore item') }}">
    <x-slot:body>
        <form class="single-restore-form" action="{{ $action }}" method="POST" id="single-restore-form">
            @csrf
            @method('PATCH')

            <input type="hidden" name="ids[]">
            <p>{{ __('Restore item from trash') }}?</p>
        </form>
    </x-slot:body>

    <x-slot:footer>
        <x-different.button style="cancel" data-click-action="hide-active-modals">{{ __('Cancel') }}</x-different.button>
        <x-different.button style="success" type="submit" form="single-restore-form">{{ __('Restore') }}</x-different.button>
    </x-slot:footer>
</x-modals.template>
