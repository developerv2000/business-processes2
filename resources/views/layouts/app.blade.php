<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('img/main/favicon.png') }}">

    <title>{{ __('Business processes') }}</title>

    @vite('resources/css/app.css')
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
    @vite('resources/js/app.js')
</body>

</html>
