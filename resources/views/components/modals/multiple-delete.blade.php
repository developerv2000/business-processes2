@props(['action', 'forceDelete'])

<x-modals.template class="multiple-delete-modal" title="{{ __('Delete records') }}">
    <x-slot:body>
        <form class="multiple-delete-form" action="{{ $action }}" data-before-submit="appends-inputs" data-inputs-selector=".main-table .td__checkbox" method="POST" id="multiple-delete-form">
            @csrf
            @method('DELETE')

            @if ($forceDelete)
                <input type="hidden" name="force_delete" value="1">
            @endif

            <p>{{ __('Are you sure, you want to delete all selected records') }}?</p>
            <p>{{ __('Also, all associated records will be deleted with it') }}!</p>

            {{-- Used to hold appended checkbox inputs before submiting form by JS --}}
            <div class="form__hidden-inputs-container"></div>
        </form>
    </x-slot:body>

    <x-slot:footer>
        <x-different.button style="cancel" data-click-action="hide-active-modals">{{ __('Cancel') }}</x-different.button>
        <x-different.button style="danger" type="submit" form="multiple-delete-form">{{ __('Delete') }}</x-different.button>
    </x-slot:footer>
</x-modals.template>
