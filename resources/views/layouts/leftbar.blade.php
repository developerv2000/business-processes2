<aside class="leftbar">
    <div class="leftbar__inner">
        <nav class="navbar navbar--vertical">
            {{-- MAD --}}
            @canany(['view-epp', 'view-kvpp', 'view-ivp', 'view-vps', 'view-meetings', 'view-kpe', 'view-spg'])
                <x-navbar.title class="navbar-title--first">{{ __('MAD') }}</x-navbar.title>
            @endcanany

            @can('view-epp')
                <x-navbar.link icon="view_list" href="{{ route('manufacturers.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('manufacturers.*'),
                ])>{{ __('EPP') }}</x-navbar.link>
            @endcan

            @can('view-kvpp')
                <x-navbar.link icon="content_paste_search" href="{{ route('kvpp.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('kvpp.*'),
                ])>{{ __('KVPP') }}</x-navbar.link>
            @endcan

            @can('view-ivp')
                <x-navbar.link icon="pill" href="{{ route('products.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('products.*'),
                ])>{{ __('IVP') }}</x-navbar.link>
            @endcan

            @can('view-vps')
                <x-navbar.link icon="stacks" href="{{ route('processes.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('processes.*'),
                ])>{{ __('VPS') }}</x-navbar.link>
            @endcan

            @can('view-meetings')
                <x-navbar.link icon="calendar_month" href="{{ route('meetings.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('meetings.*'),
                ])>{{ __('Meetings') }}</x-navbar.link>
            @endcan

            @can('view-kpe')
                <x-navbar.link icon="bar_chart" href="{{ route('statistics.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('statistics.index'),
                ])>{{ __('KPE') }}</x-navbar.link>
            @endcan

            @can('view-spg')
                <x-navbar.link icon="pie_chart" href="{{ route('plan.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('plan.*'),
                ])>{{ __('SPG') }}</x-navbar.link>
            @endcan

            {{-- Logistics --}}
            @canany(['view-processes-for-order', 'view-orders'])
                <x-navbar.title class="navbar-title navbar-title--top-margined">{{ __('ОППЛ') }}</x-navbar.title>
            @endcanany

            @can('view-processes-for-order')
                <x-navbar.link icon="grading" href="{{ route('processes_for_order.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('processes_for_order.*'),
                ])>{{ __('For order') }}</x-navbar.link>
            @endcan

            @can('view-orders')
                <x-navbar.link icon="package_2" href="{{ route('orders.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('orders.*'),
                ])>{{ __('Orders') }}</x-navbar.link>

                <x-navbar.link icon="medication" href="{{ route('order.products.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('order.products.*'),
                ])>{{ __('Products') }}</x-navbar.link>
            @endcan

            {{-- Finance --}}
            @canany(['view-processes-for-order', 'view-orders'])
                <x-navbar.title class="navbar-title navbar-title--top-margined">{{ __('ОПР') }}</x-navbar.title>

                <x-navbar.link icon="package_2" href="{{ route('confirmed-orders.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('confirmed-orders.index'),
                ])>{{ __('Orders') }}</x-navbar.link>

                <x-navbar.link icon="package_2" href="{{ route('invoices.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('invoices.*'),
                ])>{{ __('Invoices') }}</x-navbar.link>

                <x-navbar.link icon="package_2" href="{{ route('invoice-items.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('invoice-items.*'),
                ])>{{ __('Invoice items') }}</x-navbar.link>
            @endcanany

            {{-- Dashboard --}}
            @canany(['view-users', 'view-differents', 'view-roles'])
                <x-navbar.title class="navbar-title--top-margined">{{ __('Dashboard') }}</x-navbar.title>
            @endcanany

            @can('view-differents')
                <x-navbar.link icon="dataset" href="{{ route('templated-models.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('templated-models.*'),
                ])>{{ __('Different') }}</x-navbar.link>
            @endcan

            @can('view-users')
                <x-navbar.link icon="account_circle" href="{{ route('users.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('users.*'),
                ])>{{ __('Users') }}</x-navbar.link>
            @endcan

            @can('view-roles')
                <x-navbar.link icon="manage_accounts" href="{{ route('roles.index') }}" @class([
                    'navbar-link--active' => request()->routeIs('roles.index'),
                ])>{{ __('Roles') }}</x-navbar.link>
            @endcan
        </nav>
    </div>
</aside>
