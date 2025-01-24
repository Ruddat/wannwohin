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
    <link rel="stylesheet" href="{{ asset('assets\css\flag-icons.css') }}">

    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">

    <!-- Cookies Consent CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/cookie-consent.css') }}">
    @yield('css')
    @stack('css')
    <!-- Head Libs -->
    <script src="{{ asset('assets/vendor/modernizr/modernizr.min.js') }}"></script>
</head>
<body data-plugin-scroll-spy data-plugin-options="{'target': '.wrapper-spy'}">
    <livewire:frontend.quick-search.quick-search-component />
                    {{--
    @include('layouts.search')
--}}


<!-- Ladeanzeige -->
<div id="loading-screen" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, #4CAF50, #2196F3); z-index: 9999; display: flex; align-items: center; justify-content: center; flex-direction: column;">
    <div class="loading-animation">
        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#ffffff" class="bi bi-globe" viewBox="0 0 16 16">
            <path d="M8.001 15.999c3.866 0 7-3.133 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zM2.318 8c0 3.13 2.55 5.681 5.682 5.681.176 0 .356-.005.533-.015-.008-.115-.013-.23-.017-.346-.044-1.423-.133-4.58-2.134-5.885-.956-.636-2.1-.804-3.159-.435-.174-.996-.26-2.068-.26-3.117 0-.063.002-.125.003-.188A5.58 5.58 0 0 1 2.318 8zm11.364 0a5.58 5.58 0 0 1-1.64 3.966 5.53 5.53 0 0 1-1.25-2.847A15.67 15.67 0 0 1 12.41 8c-.037-.36-.09-.714-.16-1.062-.32-.036-.648-.059-.979-.059-.2 0-.399.006-.595.016-.094-.392-.196-.781-.307-1.166a5.58 5.58 0 0 1 3.011 1.184 5.58 5.58 0 0 1 1.442 1.818c.054.093.102.186.146.28.098-.292.17-.593.208-.896z"/>
        </svg>
    </div>
    <p style="color: #fff; font-size: 1.5rem; margin-top: 20px;">Laden... Wir bereiten alles für Sie vor!</p>
    <div class="loading-bar" style="width: 80%; margin-top: 20px;">
        <div class="progress"></div>
    </div>
</div>



@if (Route::is('home', 'impressum', 'search.results', 'detail_search', 'continent.countries', 'list-country-locations', 'ergebnisse.anzeigen'))

    @php
        // Daten aus der Session oder übergebenen Variablen abrufen
        $panoramaLocationPicture = session('headerData.bgImgPath') ?? $panorama_location_picture ?? null;
        $mainLocationPicture = session('headerData.mainImgPath') ?? $main_location_picture ?? null;
        $panoramaLocationText = session('headerData.headerContent.main_text') ?? $panorama_location_text ?? 'Standardtext';
    @endphp

    <x-header
        :panorama-location-picture="$panoramaLocationPicture"
        :main-location-picture="$mainLocationPicture"
        :panorama-location-text="$panoramaLocationText"
    />

@else

    <x-header-details
        :pic1-text="$pic1_text ?? 'Standardtext 1'"
        :pic2-text="$pic2_text ?? 'Standardtext 2'"
        :pic3-text="$pic3_text ?? 'Standardtext 3'"
        :head-line="$head_line ?? 'Standardüberschrift'"
        :gallery-images="$gallery_images ?? []"
    />

@endif


<div class="bg-white">
    <div class="container-fluid main-content-section">

        @show
        @yield('content')
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


    <!-- Theme Initialization Files -->
    <script src="{{ asset('assets/js/theme.init.js') }}"></script>

    <!-- jquery UI extra added from hani.masoud@gmx.de -->
    <script src={{ asset('assets/js/jquery-ui.js') }}></script>

    <!-- Cookies Consent added from hani.masoud@gmx.de -->
    <script src={{ asset('assets/js/cookie-consent.js') }}></script>


    <!-- AOS JS -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<script>
    let token = '{{ csrf_token() }}';
    let mainUrl = '{{ url('/') }}';
    window.mainUrl = '{{ url('/') }}';
</script>



<style>
    /* Animation Styles */
    #loading-screen {
        animation: fadeOut 0.5s ease-in-out forwards;
    }

    .loading-animation {
        text-align: center;
        animation: bounce 1.5s infinite;
    }

    .loading-bar {
        background: #ddd;
        height: 10px;
        border-radius: 5px;
        overflow: hidden;
        position: relative;
    }

    .progress {
        width: 0%;
        height: 100%;
        background: linear-gradient(90deg, #ff5722, #ff9800, #ffc107);
        animation: progress 2s infinite;
    }

    @keyframes fadeOut {
        0% {
            opacity: 1;
        }
        100% {
            opacity: 0;
            visibility: hidden;
        }
    }

    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    @keyframes progress {
        0% {
            width: 0%;
        }
        50% {
            width: 80%;
        }
        100% {
            width: 100%;
        }
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const loadingScreen = document.getElementById("loading-screen");

    // Funktion zum Aktivieren der Ladeanzeige
    const showLoadingScreen = () => {
        loadingScreen.style.display = "flex";
    };

    // Ladeanzeige ausblenden, wenn die neue Seite vollständig geladen ist
    window.addEventListener("load", () => {
        loadingScreen.style.display = "none";
    });

    // Ladeanzeige bei Klick auf Links
    document.addEventListener("click", (e) => {
        const target = e.target.closest("a"); // Nächsten <a>-Tag finden
        if (target && target.getAttribute("href") && target.getAttribute("href") !== "#" && !target.hasAttribute('data-no-loading')) {
            showLoadingScreen();
        }
    });

    // Ladeanzeige bei Formular-Submit
    document.addEventListener("submit", (e) => {
        const target = e.target; // Aktuelles Formular
        if (target.tagName === "FORM") {
            showLoadingScreen();
        }
    });
});



</script>



@yield('js')
@stack('js')

</body>
</html>

