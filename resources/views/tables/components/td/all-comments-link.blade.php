<a class="td__link" href="{{ route('comments.index', [class_basename($instance), $instance->id]) }}">
    {{ $instance->comments_count }} {{ __('comments') }}
</a>
