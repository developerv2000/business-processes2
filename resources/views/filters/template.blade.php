<aside class="rightbar styled-box thin-scrollbar">
    <div class="filter">
        <div class="filter__header">
            <h3 class="filter__title">{{ __('Filter') }}</h3>

            <a class="filter__reset" title="{{ __('Reset') }}" href="{{ url()->current() }}">
                <span class="material-symbols-outlined">restart_alt</span>
            </a>
        </div>


        <form class="form filter-form" action="{{ url()->current() }}" method="GET">
            {{-- Save current ordering --}}
            <input type="hidden" name="orderBy" value="{{ $request->orderBy }}">
            <input type="hidden" name="orderType" value="{{ $request->orderType }}">

            @yield('elements')

            <x-different.button type="submit" class="fiter-form__submit">{{ __('Update') }}</x-different.button>
        </form>
    </div>
</aside>
