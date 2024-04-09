@props(['action', 'table', 'columns'])

<x-modals.template class="edit-table-columns-modal" title="{{ __('Setup columns') }}">
    <x-slot:body>
        <form class="table-columns-edit-form" action="{{ $action }}" method="POST" id="table-columns-edit-form">
            @csrf
            @method('PATCH')
            <input type="hidden" name="table" value="{{ $table }}">

            <p>{{ __('Drag and drop columns for sorting!') }}</p>
            <x-modals.partials.sortable-table-columns :columns="$columns" />
        </form>
    </x-slot:body>

    <x-slot:footer>
        <x-different.button style="cancel" data-click-action="hide-active-modals">{{ __('Close') }}</x-different.button>
        <x-different.button style="main" type="submit" form="table-columns-edit-form">{{ __('Update') }}</x-different.button>
    </x-slot:footer>
</x-modals.template>
