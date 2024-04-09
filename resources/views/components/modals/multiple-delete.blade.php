@props(['action', 'forceDelete'])

<x-modals.template class="multiple-delete-modal" title="{{ __('Delete items') }}">
    <x-slot:body>
        <form class="multiple-delete-form" action="{{ $action }}" method="POST" id="multiple-delete-form">
            @csrf
            @method('DELETE')

            <input type="hidden" name="force_delete" value="{{ $forceDelete }}">

            <p>{{ __('Are you sure, you want to delete all selected items?') }}</p>
            <p>{{ __('All elements associated with it will also be deleted!') }}</p>
        </form>
    </x-slot:body>

    <x-slot:footer>
        <x-different.button style="cancel" data-click-action="hide-active-modals">{{ __('Cancel') }}</x-different.button>
        <x-different.button style="danger" type="submit" form="multiple-delete-form">{{ __('Delete') }}</x-different.button>
    </x-slot:footer>
</x-modals.template>
