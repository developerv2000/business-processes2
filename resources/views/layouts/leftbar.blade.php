<aside class="leftbar">
    <div class="leftbar__inner">
        <nav class="navbar">
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

            <x-navbar.link icon="calendar_month" href="{{ route('manufacturers.index') }}" @class([
                'navbar-link--active' => request()->routeIs('template.*'),
            ])>{{ __('Meetings') }}</x-navbar.link>

            <x-navbar.link icon="bar_chart" href="{{ route('statistics.index') }}" @class([
                'navbar-link--active' => request()->routeIs('statistics.*'),
            ])>{{ __('КПЭ') }}</x-navbar.link>
        </nav>
    </div>
</aside>
