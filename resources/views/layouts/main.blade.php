<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Basic -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
{{--    <title>@yield('title')</title>--}}
    @yield('meta')
{{--    <meta name="keywords" content="HTML5 Template" />--}}
{{--    <meta name="description" content="Porto - Responsive HTML5 Template">--}}
{{--    <meta name="author" content="okler.net">--}}

    <!-- Favicon -->
{{--    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />--}}
{{--    <link rel="apple-touch-icon" href="img/apple-touch-icon.png">--}}

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">

    <!-- Web Fonts  -->
    <link id="googleFonts" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800&display=swap" rel="stylesheet" type="text/css">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/animate/animate.compat.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/simple-line-icons/css/simple-line-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/owl.carousel/assets/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/owl.carousel/assets/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/magnific-popup/magnific-popup.min.css') }}">

    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme-elements.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme-blog.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme-shop.css') }}">

    <!-- Demo CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/demo-resume.css') }}">

    <!-- Skin CSS -->
    <link id="skinCSS" rel="stylesheet" href="{{ asset('assets/css/skin-resume.css') }}">

    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/footer.css') }}">

    <!-- jquery UI extra added from hani.masoud@gmx.de -->
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.theme.css') }}"/>

    <!-- Slider Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom-slider.css') }}">


    <!-- Flag Icons CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/flag-icons-master/flag-icon.css') }}">

    <!-- Cookies Consent CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/cookie-consent.css') }}">
    @yield('css')

    <!-- Head Libs -->
    <script src="{{ asset('assets/vendor/modernizr/modernizr.min.js') }}"></script>
</head>
<body data-plugin-scroll-spy data-plugin-options="{'target': '.wrapper-spy'}">
    <livewire:frontend.quick-search.quick-search-component />
                    {{--
    @include('layouts.search')
--}}



<x-header
    :panorama-location-picture="$panorama_location_picture ?? null"
    :main-location-picture="$main_location_picture ?? null"
    :panorama-location-text="$panorama_location_text ?? null"
/>

<div class="bg-white">
    <div class="container-fluid main-content-section">
        @section('content')
        @show
    </div>
</div>
<!-- cookie consent-->
@include('partials.cookie_consent')
<!-- END cookie consent-->

@include('layouts.footer')

    <!-- Vendor -->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery.appear/jquery.appear.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery.easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery.cookie/jquery.cookie.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery.validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery.easy-pie-chart/jquery.easypiechart.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery.gmap/jquery.gmap.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/lazysizes/lazysizes.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/isotope/jquery.isotope.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/owl.carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/vide/jquery.vide.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/vivus/vivus.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/moment/moment-with-locales.min.js') }}"></script>

    <!-- Theme Base, Components and Settings -->
    <script src="{{ asset('assets/js/theme.js') }}"></script>

    <!-- Current Page Vendor and Views -->
    <script src="{{ asset('assets/js/view.contact.js') }}"></script>

    <!-- Demo -->
    <script src="{{ asset('assets/js/demo-resume.js') }}"></script>

    <!-- Theme Custom -->
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/quick_search.js') }}"></script>
    <script src="{{ asset('assets/js/details_search.js') }}"></script>

    <!-- Theme Initialization Files -->
    <script src="{{ asset('assets/js/theme.init.js') }}"></script>

    <!-- jquery UI extra added from hani.masoud@gmx.de -->
    <script src={{ asset('assets/js/jquery-ui.js') }}></script>

    <!-- flags icons js -->
    <script src="{{ asset('assets/js/flag_icons.js') }}"></script>


    <!-- Cookies Consent added from hani.masoud@gmx.de -->
    <script src={{ asset('assets/js/cookie-consent.js') }}></script>
<script>
    let token = '{{ csrf_token() }}';
    let mainUrl = '{{ url('/') }}';
    window.mainUrl = '{{ url('/') }}';
</script>

    @yield('js')
</body>
</html>

