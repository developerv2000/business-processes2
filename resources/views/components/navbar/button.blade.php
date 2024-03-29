@props(['icon'])

<button class="navbar-button">
    <span class="material-symbols-outlined navbar-button__icon">{{ $icon }}</span>
    <span class="navbar-button__text">{{ $slot }}</span>
</button>
