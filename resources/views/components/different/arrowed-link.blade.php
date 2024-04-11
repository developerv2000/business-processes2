@props(['href'])

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'button button--transparent arrowed-link']) }}>
    <span class="button__text">{{ $slot }}</span>
    <span class="button__icon material-symbols-outlined">arrow_forward</span>
</a>
