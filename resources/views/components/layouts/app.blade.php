<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ config('app.name', 'Terminal Portfolio') }}</title>

        {{-- Fonts --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=jetbrains-mono:400,500,600,700" rel="stylesheet" />

        {{-- Scripts --}}
        @vite(['resources/css/terminal.css', 'resources/js/app.js'])
    </head>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <body class="antialiased bg-black h-full m-0 p-0 text-sm">
        {{ $slot }}

        @livewireScripts
    </body>

</html>