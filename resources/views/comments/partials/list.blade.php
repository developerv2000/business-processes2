@if ($instance->comments->count())
    <div class="comments-list">
        @foreach ($instance->comments as $comment)
            <div class="comments-list__item">
                <div class="comments-list__header">
                    <x-different.ava
                        class="comments-list__ava"
                        image="{{ $comment->user->photo_asset_path }}"
                        title="{{ $comment->user->name }}"
                        description="{{ $comment->created_at->diffForHumans() }}">
                    </x-different.ava>

                    @if (request()->user()->isAdministrator())
                        <div class="comments-list__actions">
                            <x-different.linked-button
                                style="main"
                                class="button--rounded"
                                href="{{ route('comments.edit', $comment->id) }}"
                                icon="edit" />

                            <x-different.button
                                style="danger"
                                class="button--rounded"
                                icon="delete"
                                data-click-action="show-targeted-modal"
                                data-modal-selector=".target-delete-modal"
                                :data-target-id="$comment->id" />
                        </div>
                    @endif
                </div>

                <div class="comments-list__item-body">
                    {{ $comment->body }}
                </div>
            </div>
        @endforeach
    </div>
@endif
