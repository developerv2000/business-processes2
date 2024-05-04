<a class="td__link" href="{{ route('comments.index', [get_class($instance), $instance->id]) }}">
    {{ $instance->comments_count }} {{ __('comments') }}
</a>
