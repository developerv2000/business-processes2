@props(['image', 'text' => null, 'desc' => null])

<div class="ava {{ $attributes['class'] }}">
    <img class="ava__image" src="{{ $image }}">

    @if ($text)
        <span class="ava__text">{{ $text }}</span>
    @endif
</div>
