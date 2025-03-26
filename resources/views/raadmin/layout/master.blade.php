{{-- resources\views\raadmin\layout\master.blade.php --}}

<!DOCTYPE html>
<html lang="{{ config('site.language', 'de') }}">

<head>
    <!-- All meta and title start -->
    @include('raadmin.layout.head')
    <!-- meta and title end -->

    <!-- css start -->
    @include('raadmin.layout.css')

    @stack('styles') <!-- Seiten-spezifische Styles -->
    <!-- css end -->
    <!-- Livewire Styles -->
    @livewireStyles
    @vite(['resources/backend/css/app.css']) <!-- Vite für CSS -->
</head>

<body>
    <!-- Loader start -->
    <div class="app-wrapper">
        <!-- Loader end -->


        <div class="loader-wrapper">
            <div class="loader_16"></div>
          </div>


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
    @include('raadmin.layout.script')
    <!-- Livewire Scripts -->
    @livewireScripts
    @vite(['resources/backend/js/app.js']) <!-- Vite für JS -->
    @stack('scripts') <!-- Seiten-spezifische Skripte -->
    <!-- scripts end -->
</body>

</html>
