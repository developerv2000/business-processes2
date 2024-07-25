<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('img/main/favicon.png') }}">

    <title>{{ __('Business processes') }}</title>

    @vite('resources/css/app.css')
    @vite('resources/css/themes/' . request()->user()->settings['theme'] . '.css')
</head>

<body class="body {{ $page }}">
    <div @class([
        'body__inner',
        'body__inner--shrinked' => request()->user()->settings['shrink_body_width'],
    ])>

        @include('layouts.header')

        <div class="main-wrapper">
            @include('layouts.leftbar')

            <main class="main">
                @yield('main')
            </main>

            @hasSection('rightbar')
                @yield('rightbar')
            @endif

            <x-different.spinner />
        </div>
    </div>

    {{-- JQuery --}}
    <script src="{{ asset('plugins/jquery/jquery-3.6.4.min.js') }}"></script>

    {{-- Selectize --}}
    <script src="{{ asset('plugins/selectize/selectize.min.js') }}"></script>
    <script src="{{ asset('plugins/selectize/preserve-on-blur-plugin/preserve-on-blur.js') }}"></script>

    {{-- Moment.js (required in Date range picker) --}}
    <script src="{{ asset('plugins/moment.min.js') }}"></script>

    {{-- JQuery Date range picker --}}
    <script src="{{ asset('plugins/date-range-picker/daterangepicker.min.js') }}"></script>

    {{-- Simditor v2.3.28 --}}
    <script src="{{ asset('plugins/simditor/module.js') }}"></script>
    <script src="{{ asset('plugins/simditor/hotkeys.js') }}"></script>
    <script src="{{ asset('plugins/simditor/uploader.js') }}"></script>
    <script src="{{ asset('plugins/simditor/simditor.js') }}"></script>

    {{-- JQuery UI. (required in nested sortable)  --}}
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>

    {{-- JQuery Nested Sortable --}}
    <script src="{{ asset('plugins/jq-nested-sortable/jq-nested-sortable.js') }}"></script>

    {{-- Apache ECharts --}}
    <script src="{{ asset('plugins/echarts/echarts.min.js') }}"></script>

    @vite('resources/js/app.js')
</body>

</html>
