@props(['action', 'instanceId', 'forceDelete'])

<x-modals.template class="single-delete-modal" title="{{ __('Delete record') }}">
    <x-slot:body>
        <form class="single-delete-form" action="{{ $action }}" method="POST" id="single-delete-form">
            @csrf
            @method('DELETE')

            @if ($forceDelete)
                <input type="hidden" name="force_delete" value="1">
            @endif

            <input type="hidden" name="id" value="{{ $instanceId }}">

            <p>{{ __('Are you sure, you want to delete record') }}?</p>
            <p>{{ __('Also, all associated records will be deleted with it') }}!</p>
        </form>
    </x-slot:body>

    <x-slot:footer>
        <x-different.button style="cancel" data-click-action="hide-active-modals">{{ __('Cancel') }}</x-different.button>
        <x-different.button style="danger" type="submit" form="single-delete-form">{{ __('Delete') }}</x-different.button>
    </x-slot:footer>
</x-modals.template>
