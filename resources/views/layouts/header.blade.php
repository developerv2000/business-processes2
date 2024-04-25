<header class="header">
    <div class="header__inner main-container">
        <div class="header__left">
            <button class="leftbar-toggler">
                <span class="material-symbols-outlined">hide</span>
            </button>

            <x-different.logo class="header__logo" />
        </div>

        <div class="header__right">
            {{-- Locales dropdown --}}
            <div class="dropdown locales-dropdown">
                <x-different.locales-button class="dropdown__button" :value="app()->getLocale()">
                    {{ app()->getLocale() }}
                </x-different.locales-button>

                <div class="dropdown__content">
                    <form class="update-locale-form" action="{{ route('settings.update-locale') }}" method="POST">
                        @method('PATCH')
                        @csrf

                        <x-different.locales-button value="en">English</x-different.locales-button>
                        <x-different.locales-button value="ru">Русский</x-different.locales-button>
                    </form>
                </div>
            </div>

            {{-- Profile dropdown --}}
            <div class="dropdown profile-dropdown">
                <div class="dropdown__button">
                    <x-different.ava image="{{ request()->user()->photo_asset_path }}"></x-different.ava>
                </div>

                <div class="dropdown__content">
                    <x-navbar.link icon="face" href="{{ route('profile.edit') }}">{{ __('My profile') }}</x-navbar.link>

                    <form action="/logout" method="POST">
                        @csrf
                        <x-navbar.button icon="exit_to_app">{{ __('Logout') }}</x-navbar.button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
