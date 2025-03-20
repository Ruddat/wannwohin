<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <!-- Meta-Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Für Livewire/AJAX -->
    <title>{{ config('app.name', 'Location Manager') }} - @yield('title')</title>

    <!-- CSS -->
    @include('backend.layouts.partials.header-css') <!-- Partielle CSS-Einbindung -->
    @vite(['resources/backend/css/app.css']) <!-- Vite für CSS -->

    <!-- Livewire Styles -->
    @livewireStyles
    @stack('styles') <!-- Seiten-spezifische Skripte -->
</head>
<body class="layout-fluid">
    <div class="page">
        <!-- Sidebar -->
        @include('backend.layouts.partials.sidebar')

        <div class="page-wrapper">
            <!-- Main Section -->
            <div class="page-body">
                @yield('main-content') <!-- Traditionelle Blade-Inhalte -->
                {{ $slot }} <!-- Livewire-Komponenten -->
            </div>
        </div>
    </div>

    <!-- Skripte -->
    @vite(['resources/backend/js/app.js']) <!-- Vite für JS -->
    <script src="{{ asset('backend/dist/js/demo-theme.min.js') }}"></script> <!-- Ohne manuelle Version -->
    @include('backend.layouts.partials.scripts') <!-- Partielle Skripte -->
    @livewireScripts <!-- Livewire-Skripte -->
    @stack('scripts') <!-- Seiten-spezifische Skripte -->
</body>
</html>
