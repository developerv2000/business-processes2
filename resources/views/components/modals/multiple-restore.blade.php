@props(['action'])

<x-modals.template class="multiple-restore-modal" title="{{ __('Restore records') }}">
    <x-slot:body>
        <form class="multiple-restore-form" action="{{ $action }}" data-before-submit="appends-inputs" data-inputs-selector=".main-table .td__checkbox" method="POST" id="multiple-restore-form">
            @csrf
            @method('PATCH')

            <p>{{ __('Restore records from trash') }}?</p>

            {{-- Used to hold appended checkbox inputs before submiting form by JS --}}
            <div class="form__hidden-inputs-container"></div>
        </form>
    </x-slot:body>

    <x-slot:footer>
        <x-different.button style="cancel" data-click-action="hide-active-modals">{{ __('Cancel') }}</x-different.button>
        <x-different.button style="success" type="submit" form="multiple-restore-form">{{ __('Restore') }}</x-different.button>
    </x-slot:footer>
</x-modals.template>
