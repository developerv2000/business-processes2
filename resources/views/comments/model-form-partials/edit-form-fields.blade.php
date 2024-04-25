<div class="form__section">
    @if ($instance->lastComment)
        <x-forms.groups.default-group label="{{ __('Last comment') }}">
            <textarea class="textarea" rows="5" readonly>{{ $instance->lastComment->body }}</textarea>
        </x-forms.groups.default-group>
    @endif

    <x-forms.textarea.default-textarea
        label="Add new comment"
        name="comment" />
</div>
