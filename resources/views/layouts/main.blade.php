<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Basic -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    {{--    <title>@yield('title')</title> --}}
    @yield('meta')
    {{--    <meta name="keywords" content="HTML5 Template" /> --}}
    {{--    <meta name="description" content="Porto - Responsive HTML5 Template"> --}}
    {{--    <meta name="author" content="okler.net"> --}}

    <!-- Favicon -->
    {{--    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" /> --}}
    {{--    <link rel="apple-touch-icon" href="img/apple-touch-icon.png"> --}}

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">

    <!-- Web Fonts  -->
    <link id="googleFonts" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800&display=swap"
        rel="stylesheet" type="text/css">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets\vendor\fontawesome-free-6.7.2-web\css\all.min.css') }}">

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
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.theme.css') }}" />

    <!-- Slider Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom-slider.css') }}">


    <!-- Flag Icons CSS -->
    <link rel="stylesheet" href="{{ asset('assets\css\flag-icons.css') }}">

    <!-- AOS CSS -->
    <link rel="stylesheet" href="{{ asset('assets\vendor\aos\aos.css') }}">

    <!-- Custom New CSS -->
    <link rel="stylesheet" href="{{ asset('assets\css\custom-new.css') }}">

    <!-- Cookies Consent CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/cookie-consent.css') }}">
    @yield('css')
    @stack('css')
    <!-- Head Libs -->
    <script src="{{ asset('assets/vendor/modernizr/modernizr.min.js') }}"></script>
</head>

<body>
    <livewire:frontend.quick-search.quick-search-component />
    {{--
    @include('layouts.search')
--}}


    <!-- Ladeanzeige -->
    <div id="loading-screen"
        style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, #4CAF50, #2196F3); z-index: 9999; display: flex; align-items: center; justify-content: center; flex-direction: column;">
        <div class="loading-animation">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#ffffff" class="bi bi-globe"
                viewBox="0 0 16 16">
                <path
                    d="M8.001 15.999c3.866 0 7-3.133 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zM2.318 8c0 3.13 2.55 5.681 5.682 5.681.176 0 .356-.005.533-.015-.008-.115-.013-.23-.017-.346-.044-1.423-.133-4.58-2.134-5.885-.956-.636-2.1-.804-3.159-.435-.174-.996-.26-2.068-.26-3.117 0-.063.002-.125.003-.188A5.58 5.58 0 0 1 2.318 8zm11.364 0a5.58 5.58 0 0 1-1.64 3.966 5.53 5.53 0 0 1-1.25-2.847A15.67 15.67 0 0 1 12.41 8c-.037-.36-.09-.714-.16-1.062-.32-.036-.648-.059-.979-.059-.2 0-.399.006-.595.016-.094-.392-.196-.781-.307-1.166a5.58 5.58 0 0 1 3.011 1.184 5.58 5.58 0 0 1 1.442 1.818c.054.093.102.186.146.28.098-.292.17-.593.208-.896z" />
            </svg>
        </div>
        <p style="color: #fff; font-size: 1.5rem; margin-top: 20px;">Laden... Wir bereiten alles für Sie vor!</p>
        <div class="loading-bar"
            style="width: 80%; height: 10px; background: rgba(255, 255, 255, 0.3); border-radius: 5px; overflow: hidden; margin-top: 20px;">
            <div class="progress" style="width: 0%; height: 100%; background: #fff; transition: width 0.3s ease;"></div>
        </div>
    </div>


    <!-- Go-to-Top Button -->
    <button id="goTopButton" class="go-top">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
            <path fill="currentColor" d="M12 2l-7 7h4v7h6v-7h4l-7-7z"></path>
        </svg>
        <svg class="progress-ring" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <circle class="progress-ring__background" cx="50" cy="50" r="45" />
            <circle class="progress-ring__progress" cx="50" cy="50" r="45" />
        </svg>
    </button>


    <style>
        /* General Button Styling */
        .go-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 4rem;
            height: 4rem;
            border: none;
            border-radius: 50%;
            background: linear-gradient(135deg, #4caf50, #81c784);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, transform 0.3s ease;
            z-index: 1;
        }

        .go-top:hover {
            background: linear-gradient(135deg, #388e3c, #66bb6a);
            transform: scale(1.1);
        }

        .go-top.visible {
            opacity: 1;
            visibility: visible;
        }

        /* Progress Ring */
        .progress-ring {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
            z-index: 0;
            /* Below the button content */
        }

        .progress-ring__background,
        .progress-ring__progress {
            fill: none;
            stroke-width: 5;
            r: 45;
            /* Radius */
            cx: 50;
            /* Center x */
            cy: 50;
            /* Center y */
        }

        .progress-ring__background {
            stroke: rgba(255, 255, 255, 0.2);
        }

        .progress-ring__progress {
            stroke: white;
            stroke-dasharray: 283;
            /* Circumference: 2 * π * r */
            stroke-dashoffset: 283;
            /* Start fully hidden */
            transition: stroke-dashoffset 0.2s ease;
        }

        @media (max-width: 768px) {
            #goTopButton {
                display: none !important;
            }
        }
    </style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const progressCircle = document.querySelector(".progress-ring__progress");
        const button = document.getElementById("goTopButton");

        if (!progressCircle || !button) return; // Falls die Elemente fehlen, beende das Script

        // Versuche, den Radius zu holen (Fehlertoleranz für nicht unterstützte Browser)
        let radius = 0;
        try {
            radius = progressCircle.r.baseVal.value;
        } catch (error) {
            console.warn("Fehler beim Laden des Fortschrittskreises:", error);
        }

        const circumference = 2 * Math.PI * radius;

        // Set initial attributes
        progressCircle.style.strokeDasharray = `${circumference}`;
        progressCircle.style.strokeDashoffset = circumference;

        // Scroll Event
        document.addEventListener("scroll", () => {
            const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
            const scrollPosition = window.scrollY;

            // Fortschrittsberechnung
            const progress = scrollPosition / scrollHeight;
            const offset = circumference - progress * circumference;

            progressCircle.style.strokeDashoffset = offset;

            // Button sichtbar machen
            if (scrollPosition > 200) {
                button.classList.add("visible");
            } else {
                button.classList.remove("visible");
            }
        });

        // Smooth Scroll (alternative Methode für bessere Firefox-Kompatibilität)
        button.addEventListener("click", () => {
            smoothScrollTo(0, 500); // 500ms Scroll-Zeit
        });

        function smoothScrollTo(targetPosition, duration) {
            const startPosition = window.scrollY;
            const distance = targetPosition - startPosition;
            let startTime = null;

            function animation(currentTime) {
                if (!startTime) startTime = currentTime;
                const timeElapsed = currentTime - startTime;
                const progress = Math.min(timeElapsed / duration, 1);

                window.scrollTo(0, startPosition + distance * easeInOutQuad(progress));

                if (timeElapsed < duration) {
                    requestAnimationFrame(animation);
                }
            }

            function easeInOutQuad(t) {
                return t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
            }

            requestAnimationFrame(animation);
        }
    });
</script>


@php
    // Daten aus der Session abrufen oder Standardwerte setzen
    $headerData = session('headerData', []);
    $panoramaLocationPicture = $headerData['bgImgPath'] ?? null;
    $mainLocationPicture = $headerData['mainImgPath'] ?? null;
    $headerTitle = $headerData['title'] ?? 'Standard Titel';
    $headerTitleText = $headerData['title_text'] ?? 'Standard Titel-Text';
    $panoramaLocationText = $headerData['main_text'] ?? null;

    // Falls kein Header-Content vorhanden ist, alternative Bilder und Texte verwenden
    $pic1_text = $pic1_text ?? 'Standardtext 1';
    $pic2_text = $pic2_text ?? 'Standardtext 2';
    $pic3_text = $pic3_text ?? 'Standardtext 3';
    $head_line = $head_line ?? 'Standardüberschrift';
    $gallery_images = $gallery_images ?? [];
@endphp

@if (Route::is(
        'home',
        'impressum',
        'search.results',
        'detail_search',
        'continent.countries',
        'list-country-locations',
        'compare',
        'ergebnisse.anzeigen'))

    @if($panoramaLocationPicture || $mainLocationPicture)
        <x-header
            :panorama-location-picture="$panoramaLocationPicture"
            :main-location-picture="$mainLocationPicture"
            :panorama-location-text="$panoramaLocationText"
        />
    @else
        <x-header-details
            :pic1-text="$pic1_text"
            :pic2-text="$pic2_text"
            :pic3-text="$pic3_text"
            :head-line="$head_line"
            :gallery-images="$gallery_images"
        />
    @endif

@else
    {{-- **Fallback für alle anderen Routen** --}}
    <x-header-details
        :pic1-text="$pic1_text"
        :pic2-text="$pic2_text"
        :pic3-text="$pic3_text"
        :head-line="$head_line"
        :gallery-images="$gallery_images"
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
<script src="{{ asset('assets\vendor\aos\aos.js') }}"></script>

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

        0%,
        100% {
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
        const progress = document.querySelector(".loading-bar .progress");
        const loadingScreen = document.getElementById("loading-screen");

        let progressValue = 0;
        const interval = setInterval(() => {
            progressValue += 10; // Fortschritt in % erhöhen
            progress.style.width = progressValue + "%";

            if (progressValue >= 100) {
                clearInterval(interval); // Fortschrittsanimation stoppen
                setTimeout(() => {
                    loadingScreen.style.opacity = "0"; // Sanft ausblenden
                    loadingScreen.style.transition = "opacity 0.5s ease";
                    setTimeout(() => {
                        loadingScreen.style.display =
                        "none"; // Entfernen nach dem Ausblenden
                    }, 500);
                }, 300);
            }
        }, 200); // Intervall alle 200 ms

    });
</script>



@yield('js')
@stack('js')

</body>

</html>
