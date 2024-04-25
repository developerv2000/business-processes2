@props(['action', 'targetId', 'forceDelete'])

<x-modals.template class="target-delete-modal" title="{{ __('Delete record') }}">
    <x-slot:body>
        <form class="target-delete-form" action="{{ $action }}" method="POST" id="target-delete-form">
            @csrf
            @method('DELETE')

            @if ($forceDelete)
                <input type="hidden" name="force_delete" value="1">
            @endif

            {{-- Input value will be generated dynamically on delete button click --}}
            <input type="hidden" name="id">

            <p>{{ __('Are you sure, you want to delete record') }}?</p>
            <p>{{ __('Also, all associated records will be deleted with it') }}!</p>
        </form>
    </x-slot:body>

    <x-slot:footer>
        <x-different.button style="cancel" data-click-action="hide-active-modals">{{ __('Cancel') }}</x-different.button>
        <x-different.button style="danger" type="submit" form="target-delete-form">{{ __('Delete') }}</x-different.button>
    </x-slot:footer>
</x-modals.template>
