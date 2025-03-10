@php
    $headerData = session('headerData', []);
    $panoramaLocationPicture = $headerData['bgImgPath'] ?? ($panorama_location_picture ?? asset('default-panorama.jpg'));
    $mainLocationPicture = $headerData['mainImgPath'] ?? ($main_location_picture ?? asset('default-main.jpg'));
    $headerTitle = $headerData['title'] ?? ($headerTitle ?? 'Standard Titel');
    $headerTitleText = $headerData['title_text'] ?? ($headerTitleText ?? 'Standard Titel-Text');
    $panoramaLocationText = $headerData['main_text'] ?? ($panorama_location_text ?? null);

    // Entferne <p>-Tags aus $headerTitleText, falls vorhanden
    $headerTitleText = str_replace(['<p>', '</p>'], '', $headerTitleText ?? '');
@endphp

<section class="custom-header-section section section-no-border section-parallax bg-transparent custom-section-padding-1 custom-position-1 custom-xs-bg-size-cover parallax-no-overflow m-0">
    <div class="parallax-background" style="background-image: url('{{ url($panoramaLocationPicture) }}');"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="custom-header-img col-lg-4 col-md-4 col-sm-12 position-relative custom-sm-margin-bottom-1">
                <img src="{{ url($mainLocationPicture) }}" class="img-fluid custom-border custom-image-position-2 custom-box-shadow-4" alt="Main Image" />
            </div>
            <div class="col-lg-6 col-xl-5 col-md-8 col-sm-12">
                <div class="heading-wrapper">
                    @if (!empty($headerTitle))
                        <h2 class="travel-heading-with-bg">
                            {!! app('autotranslate')->trans($headerTitle, app()->getLocale()) !!}
                        </h2>
                    @endif
                    @if (!empty($headerTitleText))
                        <h1 class="travel-destination">
                            {!! app('autotranslate')->trans($headerTitleText, app()->getLocale()) !!}
                        </h1>
                    @endif
                </div>
                @if (Auth::guard('admin')->check())
                    <a href="{{ route('verwaltung.site-manager.header_contents.index') }}" target="_blank" class="btn btn-primary mt-3">
                        Header Content Management
                    </a>
                @endif
            </div>
            <div class="col-lg-2 col-xl-3 d-none d-lg-block text-center">
                <img src="{{ asset('assets/img/pages/main/mouse.png') }}" class="img-fluid custom-image-pos-1" alt="Scroll Icon" />
            </div>
        </div>
    </div>
</section>

<div class="inner-shape"></div>

<div class="custom-about-links bg-color-light">
    <div class="container">
        <div class="links-row d-flex justify-content-end align-items-center gap-3">
            <div class="menu-toggle d-md-none">
                <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="collapse d-md-flex justify-content-end align-items-center gap-3" id="mobileMenu">
                <div class="link-item col-md-6 col-12">
                    @livewire('frontend.header-search.header-search-component')
                </div>
                <div class="link-item link-pair d-flex flex-wrap align-items-center gap-2">
                    <div class="sub-link-item">
                        @livewire('frontend.continent-selector.continent-selector-component')
                    </div>
                    <div class="sub-link-item">
                        @livewire('frontend.wishlist-select.wishlist-component')
                    </div>
                </div>
            </div>
        </div>
        <x-breadcrumb />
    </div>
</div>

<hr class="custom-suggestion">

@if (!empty($panoramaLocationText) && $panoramaLocationText !== null)
<div class="container">
    <div class="custom-results-text">
        <p class="custom-text">
            {!! app('autotranslate')->trans($panoramaLocationText, app()->getLocale()) !!}
        </p>
    </div>
</div>
@endif

<style>
    /* Parallax-Effekt */
    .custom-header-section {
        position: relative;
        overflow: hidden;
    }

    .parallax-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%; /* Höhe an die Sektion anpassen */
    background-position: center;
    background-size: cover;
    transform: translateZ(0);
    will-change: transform;
    z-index: -1;
}

    /* Header-Text */
    .heading-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0;
        position: relative;
        z-index: 1;
    }

    .travel-heading-with-bg {
        font-size: 1.5rem;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #333;
        background: rgba(255, 255, 255, 0.9);
        padding: 0.5rem 1rem;
        margin: 0;
        line-height: 1;
    }

    .travel-destination {
        font-size: 3rem;
        font-weight: bold;
        text-transform: uppercase;
        color: #fff;
        background: rgba(0, 0, 0, 0.7);
        padding: 0.5rem 1rem;
        margin: 0;
        line-height: 1;
        transform: translateX(3ch);
    }

    /* Hauptbild */
    .custom-header-img img {
        width: 100%;
        height: auto;
        object-fit: cover;
    }

    .custom-image-position-2 {
        border: 3px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-height: 400px;
        position: relative; /* Standardmäßig relativ */
        z-index: 0;
    }

    /* Scroll-Icon */
    .custom-image-pos-1 {
        max-width: 50px;
        animation: bounce 2s infinite;
    }

    /* About-Links */
    .custom-about-links {
        background-color: #f9f9f9;
        padding: 10px 0;
    }

    .links-row {
        gap: 15px;
    }

    .link-item {
        flex: 0 0 auto;
    }

    .custom-about-links .col-md-6 {
        max-width: 50%;
    }

    .custom-about-links .form-select {
        width: 100%;
        padding: 0.5rem;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: #FDD55C;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        color: #333;
    }

    /* Wishlist-Icon anpassen */
    .sub-link-item .wishlist-component img {
        width: 24px;
        height: 24px;
        background: none;
        border: none;
    }

    /* Animation */
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    /* Ergebnis-Text */
    .custom-results-text {
        background-color: #fdd55c;
        padding: 8px 20px;
        text-align: center;
        border-radius: 8px;
        font-weight: bold;
        font-size: 1.2rem;
        color: #333;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
        display: inline-block;
    }

    .custom-text {
        margin: 0;
        line-height: 1.4;
    }

    /* Hamburger-Menü */
    .menu-toggle {
        display: none;
    }

    /* Responsive Anpassungen */
    @media (max-width: 992px) {
        .custom-header-section {
            padding: 2rem 0;
        }
        .custom-image-position-2 {
            max-height: 300px;
            position: static; /* Im Tablet-Modus statisch */
        }
    }

    @media (max-width: 767px) {
        .menu-toggle {
            display: block;
        }
        .links-row {
            flex-wrap: wrap;
            gap: 5px;
            justify-content: flex-end;
        }
        #mobileMenu:not(.show) {
            display: none;
        }
        #mobileMenu.show {
            width: 100%;
            padding: 10px;
            background: #f9f9f9;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .link-item.col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        .link-pair {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 5px;
            width: 100%;
        }
        .sub-link-item {
            flex: 1 1 48%;
            max-width: 48%;
        }
        .travel-heading-with-bg {
            font-size: 1.2rem;
            padding: 0.4rem 0.8rem;
            text-align: center;
        }
        .travel-destination {
            font-size: 2rem;
            padding: 0.4rem 0.8rem;
            transform: none;
            text-align: center;
        }
        .custom-image-position-2 {
            max-height: 250px;
            position: static; /* Im Mobile-Modus statisch */
        }
        .custom-image-pos-1 {
            display: none;
        }
        .custom-header-section {
            padding: 1rem 0;
        }
    }

    @media (min-width: 768px) {
        .menu-toggle {
            display: none;
        }
        #mobileMenu {
            display: flex !important;
        }
        .link-pair {
            display: contents;
        }
        .sub-link-item {
            flex: 0 0 auto;
        }
        .custom-section-padding-1 {
            padding-top: 200px !important;
            padding-bottom: 70px !important;
        }
        .custom-image-position-2 {
            position: relative;
            max-width: 100%;
            top: -60px;
            z-index: 0;
            height: 400px;
        }
    }

    @media (max-width: 576px) {
        .custom-header-section {
            padding: 1rem 0;
        }
        .travel-heading-with-bg {
            font-size: 1rem;
        }
        .travel-destination {
            font-size: 1.5rem;
        }
        .custom-image-position-2 {
            max-height: 200px;
        }
        .custom-about-links {
            padding: 5px 0;
        }
        .links-row {
            gap: 8px;
        }
    }
    </style>
<script>
    document.addEventListener('scroll', function () {
        const parallax = document.querySelector('.parallax-background');
        const section = document.querySelector('.custom-header-section');
        if (parallax && section) {
            const scrollPosition = window.pageYOffset;
            const sectionHeight = section.offsetHeight;
            const maxScroll = sectionHeight; // Begrenze den Effekt an die Sektion-Höhe
            const translateY = Math.min(scrollPosition * 0.5, maxScroll); // Begrenze die Verschiebung
            parallax.style.transform = 'translateY(-' + translateY + 'px)'; // Negativ, um nach oben zu verschieben
        }
    });
    </script>
