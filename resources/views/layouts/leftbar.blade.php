<aside class="leftbar">
    <div class="leftbar__inner">
        <nav class="navbar">
            {{-- Main --}}
            <x-navbar.title class="navbar-title--first">{{ __('Main') }}</x-navbar.title>

            <x-navbar.link icon="view_list" href="{{ route('manufacturers.index') }}" @class([
                'navbar-link--active' => request()->routeIs('manufacturers.*'),
            ])>{{ __('EPP') }}</x-navbar.link>

            <x-navbar.link icon="content_paste_search" href="{{ route('kvpp.index') }}" @class([
                'navbar-link--active' => request()->routeIs('kvpp.*'),
            ])>{{ __('KVPP') }}</x-navbar.link>

            <x-navbar.link icon="pill" href="{{ route('products.index') }}" @class([
                'navbar-link--active' => request()->routeIs('products.*'),
            ])>{{ __('IVP') }}</x-navbar.link>

            <x-navbar.link icon="stacks" href="{{ route('processes.index') }}" @class([
                'navbar-link--active' => request()->routeIs('processes.*'),
            ])>{{ __('VPS') }}</x-navbar.link>

            <x-navbar.link icon="calendar_month" href="{{ route('meetings.index') }}" @class([
                'navbar-link--active' => request()->routeIs('meetings.*'),
            ])>{{ __('Meetings') }}</x-navbar.link>

            <x-navbar.link icon="bar_chart" href="{{ route('statistics.index') }}" @class([
                'navbar-link--active' => request()->routeIs('statistics.*'),
            ])>{{ __('КПЭ') }}</x-navbar.link>

            {{-- Dashboard --}}
            @if (request()->user()->isAdmin())
                <x-navbar.title class="navbar-title--top-margined">{{ __('Dashboard') }}</x-navbar.title>

                <x-navbar.link icon="account_circle" href="{{ route('users.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('users.*'),
                ])>{{ __('Users') }}</x-navbar.link>

                <x-navbar.link icon="dataset" href="{{ route('templated-models.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('templated-models.*'),
                ])>{{ __('Different') }}</x-navbar.link>
            @endif
        </nav>
    </div>
</aside>
