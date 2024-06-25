<form action="{{ route('notifications.mark-as-read') }}" data-before-submit="appends-inputs" data-inputs-selector=".main-table .td__checkbox" method="POST" id="multiple-restore-form">
    @csrf
    @method('PATCH')

    {{-- Used to hold appended checkbox inputs before submiting form by JS --}}
    <div class="form__hidden-inputs-container"></div>

    <x-different.button style="action" icon="check">{{ __('Mark as read') }}</x-different.button>
</form>
