<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <!-- All meta and title start-->

<!-- meta and title end-->

    <!-- css start-->

<!-- css end-->

@include('backend.layouts.partials.header-css')

@stack('styles')

</head>

<body  class=" layout-fluid">

    <script src="{{asset('backend/dist/js/demo-theme.min.js?1692870487')}}"></script>
    <div class="page">
        @include('backend.layouts.partials.page-header')




      <!-- Sidebar -->
      @include('backend.layouts.partials.sidebar')


      <div class="page-wrapper">
        <!-- Main Section start -->
        <div class="page-header d-print-none">
            <div class="container-xl">
              <div class="row g-2 align-items-center">
                <div class="col">
                  <!-- Page pre-title -->
                  <div class="page-pretitle">
                    Overview
                  </div>
                  <h2 class="page-title">
                    Fluid vertical layout
                  </h2>
                </div>
                <!-- Page title actions -->
                <div class="col-auto ms-auto d-print-none">
                  <div class="btn-list">
                    <span class="d-none d-sm-inline">
                      <a href="#" class="btn">
                        New view
                      </a>
                    </span>
                    <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-report">
                      <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                      Create new report
                    </a>
                    <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-report" aria-label="Create new report">
                      <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>


            {{-- main body content --}}
            @yield('main-content')
      </div>
        <!-- Main Section end -->
    </div>



    @include('backend.layouts.partials.scripts')
    <!-- Stack für Seiten-spezifische Skripte -->
    @stack('scripts')

</body>


</html>
