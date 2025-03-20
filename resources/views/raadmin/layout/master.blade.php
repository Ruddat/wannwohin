{{-- resources\views\raadmin\layout\master.blade.php --}}

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- All meta and title start -->
    @include('raadmin.layout.head')
    <!-- meta and title end -->

    <!-- css start -->
    @include('raadmin.layout.css')
    @vite(['resources/backend/css/app.css']) <!-- Vite für CSS -->
    <!-- css end -->
    <!-- Livewire Styles -->
    @livewireStyles
</head>

<body>
    <!-- Loader start -->
    <div class="app-wrapper">
        <!-- Loader end -->

        <!-- Menu Navigation start -->
        @include('raadmin.layout.sidebar')
        <!-- Menu Navigation end -->

        <div class="app-content">
            <!-- Header Section start -->
            @include('raadmin.layout.header')
            <!-- Header Section end -->

            <!-- Main Section start -->
            <main>
                {{-- main body content --}}
                @if(request()->header('X-Livewire') || isset($slot))
                    {{ $slot }}
                @else
                    @yield('main-content')
                @endif
            </main>
            <!-- Main Section end -->
        </div>

        <!-- tap on top -->
        <div class="go-top">
            <span class="progress-value">
                <i class="ti ti-arrow-up"></i>
            </span>
        </div>

        <!-- Footer Section start -->
        @include('raadmin.layout.footer')
        <!-- Footer Section end -->
    </div>

    <!-- customizer -->
    <div id="customizer"></div>

    <!-- scripts start -->
    @vite(['resources/backend/js/app.js']) <!-- Vite für JS -->
    @include('raadmin.layout.script')
    <!-- Livewire Scripts -->
    @livewireScripts
    @stack('scripts') <!-- Seiten-spezifische Skripte -->
    <!-- scripts end -->
</body>

</html>
