<header class="header">
    <div class="header__inner main-container">
        <div class="header__left">
            <button class="leftbar-toggler">
                <span class="material-symbols-outlined">hide</span>
            </button>

            <x-different.logo class="header__logo" :theme="request()->user()->settings['theme']" />
        </div>

        <div class="header__right">
            {{-- Theme toggler --}}
            <form class="header__theme-toggler-form" action="{{ route('settings.toggle-theme') }}" method="POST">
                @csrf
                @method('PATCH')

                <x-different.button
                    style="transparent"
                    class="header__theme-toggler-button"
                    title="Switch Theme"
                    icon="{{ request()->user()->settings['theme'] == 'light' ? 'dark_mode' : 'light_mode' }}">
                </x-different.button>
            </form>

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

            {{-- Notifications --}}
            <a class="header__notifications" href="{{ route('notifications.index') }}">
                @if (request()->user()->unreadNotifications->count() == 0)
                    <span class="material-symbols-outlined header__notifications-icon">notifications</span>
                @else
                    <span class="material-symbols-outlined header__notifications-icon header__notifications-icon--unread">notifications_unread</span>
                @endif
            </a>

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
