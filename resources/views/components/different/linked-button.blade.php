@props(['style' => 'main', 'icon' => null])

<a {{ $attributes->merge(['class' => 'button button--' . $style]) }}>
    @if ($icon)
        <span class="button__icon material-symbols-outlined">{{ $icon }}</span>
    @endif
    <span class="button__text">{{ $slot }}</span>
</a>
