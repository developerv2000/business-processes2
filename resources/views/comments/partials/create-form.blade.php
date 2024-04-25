<form class="comments-create-form" action="{{ route('comments.store') }}" method="POST">
    @csrf
    <input type="hidden" name="commentable_type" value="{{ get_class($instance) }}">
    <input type="hidden" name="commentable_id" value="{{ $instance->id }}">

    <x-different.ava image="{{ request()->user()->photo_asset_path }}"></x-different.ava>

    <input
        type="text"
        name="body"
        class="comments-create-form__input"
        placeholder="{{ __('Add new comment') }}"
        minlength="2"
        autocomplete="off"
        required>

    <x-different.button style="transparent" type="submit" class="comments-create-form__submit" icon="send" />
</form>
