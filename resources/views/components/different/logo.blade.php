<a href="/" {{ $attributes->merge(['class' => 'logo']) }}>
    @switch($theme)
        @case('light')
            <img class="logo__image" src="{{ asset('img/main/logo-dark.svg') }}">
        @break

        @case('dark')
            <img class="logo__image" src="{{ asset('img/main/logo-light.png') }}">
        @break

        @default
            <img class="logo__image" src="{{ asset('img/main/logo.svg') }}">
        @break
    @endswitch
</a>
