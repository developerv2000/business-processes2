@props(['icon'])

<a {{ $attributes->merge(['class' => 'navbar-link']) }}>
    <span class="material-symbols-outlined navbar-link__icon">{{ $icon }}</span>
    <span class="navbar-link__text">{{ $slot }}</span>
</a>
