<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <!-- All meta and title start-->

<!-- meta and title end-->

    <!-- css start-->

<!-- css end-->

@include('backend.layouts.partials.header-css')

@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body  class=" layout-fluid">

    <script src="{{asset('backend/dist/js/demo-theme.min.js?1692870487')}}"></script>
    <div class="page">

      <!-- Sidebar -->
      @include('backend.layouts.partials.sidebar')


      <div class="page-wrapper">
        <!-- Main Section start -->
        <div class="page-body">


            {{-- main body content --}}
            @yield('main-content')


            {{ $slot }}
        </div>
        <!-- Main Section end -->
        </div>
    </div>



    @include('backend.layouts.partials.scripts')
    <!-- Stack für Seiten-spezifische Skripte -->
    @stack('scripts')

</body>


</html>
