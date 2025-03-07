<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- Basic --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

{{-- Dynamische SEO-Daten --}}
@if (isset($seo))
    <title>{{ $seo['title'] ?? ($seo['keywords']['main'] ?? 'WannWohin.de – Dein Reiseportal für Urlaub 2025') }}</title>
    <meta name="description" content="{{ $seo['description'] ?? ($seo['keywords']['description'] ?? 'Entdecke die besten Urlaubsziele, Wetter, Klima und Direktflüge weltweit bei WannWohin.de.') }}">
    <link rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}">

    @if (isset($seo['extra_meta']) && is_array($seo['extra_meta']))
        @foreach ($seo['extra_meta'] as $key => $value)
            <meta property="{{ $key }}" content="{{ $value }}">
        @endforeach
    @endif

    {{-- Keywords: Nur tags als Meta-Keywords --}}
    <meta name="keywords" content="{{ implode(', ', $seo['keywords']['tags'] ?? []) }}">

    {{-- Dynamische Keywords-Schlüssel als zusätzliche Meta-Tags --}}
    @if (isset($seo['keywords']) && is_array($seo['keywords']))
        @foreach ($seo['keywords'] as $key => $value)
            @if ($key !== 'tags') {{-- 'tags' wird bereits separat behandelt --}}
                <meta name="keyword-{{ $key }}" content="{{ is_array($value) ? implode(', ', $value) : $value }}">
            @endif
        @endforeach
    @endif
@else
    <title>WannWohin.de – Dein Reiseportal für Urlaub 2025</title>
    <meta name="description" content="Entdecke die besten Urlaubsziele, Wetter, Klima und Direktflüge weltweit bei WannWohin.de.">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="keywords" content="Urlaub 2025, Reiseziele, Wetter, Direktflüge, Klima">
@endif

{{-- Strukturierte Daten dynamisch basierend auf dem Modell --}}
<script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": @php
            if (isset($location) && $location instanceof \App\Models\WwdeLocation) {
                echo '"TouristDestination"';
            } elseif (isset($continent) && $continent instanceof \App\Models\WwdeContinent) {
                echo '"TouristDestination"';
            } elseif (isset($country) && $country instanceof \App\Models\WwdeCountry) {
                echo '"TouristDestination"';
            } else {
                echo '"WebSite"';
            }
        @endphp,
        "name": @php
            if (isset($location)) {
                echo '"' . ($location->title ?? $seo['title'] ?? $seo['keywords']['main'] ?? 'Reiseplattform') . '"';
            } elseif (isset($continent)) {
                echo '"' . ($continent->title ?? $seo['title'] ?? $seo['keywords']['main'] ?? 'Reiseplattform') . '"';
            } elseif (isset($country)) {
                echo '"' . ($country->title ?? $seo['title'] ?? $seo['keywords']['main'] ?? 'Reiseplattform') . '"';
            } else {
                echo '"WannWohin.de – Reiseportal"';
            }
        @endphp,
        "image": "{{ $seo['image'] ?? asset('default-bg.jpg') }}",
        "description": "{{ $seo['description'] ?? ($seo['keywords']['description'] ?? 'Erkunde die besten Reiseziele und Wetterdaten weltweit.') }}",
        @if(isset($location) && $location instanceof \App\Models\WwdeLocation)
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "{{ $location->title ?? 'Unbekannter Ort' }}",
                "addressCountry": "{{ $location->iso2 ?? 'DE' }}"
            },
            "geo": {
                "@type": "GeoCoordinates",
                "latitude": "{{ $location->lat ?? 0 }}",
                "longitude": "{{ $location->lon ?? 0 }}"
            },
        @elseif(isset($country) && $country instanceof \App\Models\WwdeCountry)
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "{{ $country->title ?? 'Unbekannter Ort' }}",
                "addressCountry": "{{ $country->iso2 ?? 'DE' }}"
            },
            "geo": {
                "@type": "GeoCoordinates",
                "latitude": "{{ $country->lat ?? 0 }}",
                "longitude": "{{ $country->lon ?? 0 }}"
            },
        @elseif(isset($continent) && $continent instanceof \App\Models\WwdeContinent)
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "{{ $continent->title }}",
                "addressCountry": "Global"
            },
        @endif
        "url": "{{ $seo['canonical'] ?? url()->current() }}",
        "touristType": "Leisure",
        "keywords": "{{ implode(', ', $seo['keywords']['tags'] ?? ['Urlaub 2025', 'Reiseziele', 'Wetter', 'Direktflüge', 'Klima']) }}",
        @if(isset($seo['keywords']['nextYear']))
            "temporalCoverage": "{{ $seo['keywords']['nextYear'] }}-01-01/{{ $seo['keywords']['nextYear'] }}-12-31"
        @endif,
        @if(isset($seo['keywords']) && is_array($seo['keywords']))
            "additionalProperty": [
                @foreach ($seo['keywords'] as $key => $value)
                    @if ($key !== 'tags' && $key !== 'nextYear' && $key !== 'main' && $key !== 'description') {{-- Bekannte Schlüssel überspringen --}}
                        {
                            "@type": "PropertyValue",
                            "name": "{{ $key }}",
                            "value": "{{ is_array($value) ? implode(', ', $value) : $value }}"
                        },
                    @endif
                @endforeach
            ]
        @endif
    }
</script>


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

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
        style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, #4CAF50, #2196F3); z-index: 9999; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 20px; box-sizing: border-box;">
        <div class="loading-animation">
            <svg xmlns="http://www.w3.org/2000/svg" class="loading-icon" viewBox="0 0 16 16">
                <path
                    d="M8.001 15.999c3.866 0 7-3.133 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zM2.318 8c0 3.13 2.55 5.681 5.682 5.681.176 0 .356-.005.533-.015-.008-.115-.013-.23-.017-.346-.044-1.423-.133-4.58-2.134-5.885-.956-.636-2.1-.804-3.159-.435-.174-.996-.26-2.068-.26-3.117 0-.063.002-.125.003-.188A5.58 5.58 0 0 1 2.318 8zm11.364 0a5.58 5.58 0 0 1-1.64 3.966 5.53 5.53 0 0 1-1.25-2.847A15.67 15.67 0 0 1 12.41 8c-.037-.36-.09-.714-.16-1.062-.32-.036-.648-.059-.979-.059-.2 0-.399.006-.595.016-.094-.392-.196-.781-.307-1.166a5.58 5.58 0 0 1 3.011 1.184 5.58 5.58 0 0 1 1.442 1.818c.054.093.102.186.146.28.098-.292.17-.593.208-.896z" />
            </svg>
        </div>
        <p class="loading-text">Laden... Wir bereiten alles für Sie vor!</p>
        <div class="loading-bar">
            <div class="progress"></div>
        </div>
    </div>


    {{-- Go-to-Top Button --}}
    <button id="goTopButton" class="go-top">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
            <path fill="currentColor" d="M12 2l-7 7h4v7h6v-7h4l-7-7z"></path>
        </svg>
        <svg class="progress-ring" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <circle class="progress-ring__background" cx="50" cy="50" r="45" />
            <circle class="progress-ring__progress" cx="50" cy="50" r="45" />
        </svg>
    </button>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
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

        // Panorama-Titel und Kurztext: Falls leer, dann "STÄDTEREISE NACH {ORT}"
        $default_city = $location->title ?? 'Unbekannte Stadt';
        $panorama_titel =
            !empty($panorama_titel) && trim($panorama_titel) !== '' && $panorama_titel !== 'Standard Panorama Title'
                ? $panorama_titel
                : 'DEINE REISE NACH';

        $panorama_short_text =
            !empty($panorama_short_text) &&
            trim($panorama_short_text) !== '' &&
            $panorama_short_text !== 'Standard Panorama Short Title'
                ? $panorama_short_text
                : $default_city ?? '';

        //    $panorama_titel = !empty($headerData['panorama_titel']) ? $headerData['panorama_titel'] : __('STÄDTEREISE NACH') . ' ' . $default_city;
        // $panorama_short_text = !empty($headerData['panorama_short_text']) ? $headerData['panorama_short_text'] : $default_city;

        $gallery_images = $gallery_images ?? [];
    @endphp

    @if (Route::is(
            'home',
            'static.page',
            'impressum',
            'search.results',
            'detail_search',
            'continent.countries',
            'list-country-locations',
            'compare',
            'ergebnisse.anzeigen'))

        @if ($panoramaLocationPicture || $mainLocationPicture)
            <x-header :panorama-location-picture="$panoramaLocationPicture" :main-location-picture="$mainLocationPicture" :panorama-location-text="$panoramaLocationText" />

            @else
            <x-header-details :pic1-text="$pic1_text" :pic2-text="$pic2_text" :pic3-text="$pic3_text" :head-line="$head_line" :gallery-images="$gallery_images"
                :panorama-title="$panorama_titel" :panorama-short-text="$panorama_short_text" />
        @endif
    @else
        {{-- **Fallback für alle anderen Routen** --}}
        <x-header-details :pic1-text="$pic1_text" :pic2-text="$pic2_text" :pic3-text="$pic3_text" :head-line="$head_line" :gallery-images="$gallery_images"
            :panorama-title="$panorama_titel" :panorama-short-text="$panorama_short_text" />
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



<script>
    document.addEventListener("DOMContentLoaded", () => {
        const progress = document.querySelector(".loading-bar .progress");
        const loadingScreen = document.getElementById("loading-screen");

        let progressValue = 0;
        const interval = setInterval(() => {
            progressValue += 20; // Schnelleren Fortschritt (20% pro Schritt)
            progress.style.width = progressValue + "%";

            if (progressValue >= 100) {
                clearInterval(interval); // Fortschrittsanimation stoppen
                loadingScreen.style.transition = "opacity 0.2s ease"; // Schnellere Übergangszeit
                setTimeout(() => {
                    loadingScreen.style.opacity = "0"; // Schnelles Ausblenden
                    setTimeout(() => {
                        loadingScreen.style.display = "none"; // Schnell entfernen
                    }, 200); // Nach 200 ms entfernen
                }, 100); // Nur 100 ms warten, bevor das Ausblenden startet
            }
        }, 100); // Alle 100 ms aktualisieren (schnellerer Fortschritt)
    });
</script>



@yield('js')
@stack('js')

</body>

</html>
