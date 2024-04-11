@props(['image', 'text' => null])

<div {{ $attributes->merge(['class' => 'ava']) }}>
    <img class="ava__image" src="{{ $image }}">

    @if ($text)
        <span class="ava__text">{{ $text }}</span>
    @endif
</div>
