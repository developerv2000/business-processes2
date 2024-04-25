@props(['image', 'title' => null, 'description' => null])

<div {{ $attributes->merge(['class' => 'ava']) }}>
    <img class="ava__image" src="{{ $image }}">

    @if ($title || $description)
        <div class="ava__text">
            <span class="ava__title">{{ $title }}</span>
            <span class="ava__description">{{ $description }}</span>
        </div>
    @endif
</div>
