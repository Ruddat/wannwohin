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

<section class="custom-header-section section section-no-border section-parallax bg-transparent custom-section-padding-1 custom-xs-bg-size-cover parallax-no-overflow m-0">
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
                    <a href="{{ route('verwaltung.site-manager.header_contents.index') }}" target="_blank" class="btn btn-primary mt-2">
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

<!-- Sticky Navigation Bar -->
<div class="sticky-nav-wrapper sticky-menu">
    <div class="sticky-nav">
        <div class="logo-container">
            <a href="/">
                <img src="{{ asset('assets/ra-admin/images/logo/1-neu.png') }}" alt="Logo" class="nav-logo">
            </a>
        </div>
        <ul class="sticky-nav-list">
            <li><a href="#section-overview">Überblick</a></li>
            <li><a href="#section-highlights">Highlights</a></li>
            <li><a href="#section-climate">Klima</a></li>
            <li><a href="#section-activities">Aktivitäten</a></li>
            <li><a href="#section-recommendations">Empfehlungen</a></li>
            <li>@livewire('frontend.wishlist-select.wishlist-component')</li>
        </ul>
    </div>
</div>

    <div class="container">
        <div class="links-row d-flex justify-content-end align-items-baseline gap-2">
            <div class="menu-toggle d-md-none">
                <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="collapse d-md-flex justify-content-end align-items-baseline gap-2" id="mobileMenu">
                <div class="link-item col-md-6 col-12">
                    @livewire('frontend.header-search.header-search-component')
                </div>
                <div class="link-item link-pair d-flex flex-wrap align-items-baseline gap-2">
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
        height: 100%;
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
        font-size: 1.3rem; /* Kompakter */
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #333;
        background: rgba(255, 255, 255, 0.9);
        padding: 0.4rem 0.8rem;
        margin: 0;
        line-height: 1.2;
    }

    .travel-destination {
        font-size: 2.5rem; /* Kompakter */
        font-weight: bold;
        text-transform: uppercase;
        color: #fff;
        background: rgba(0, 0, 0, 0.7);
        padding: 0.4rem 0.8rem;
        margin: 0;
        line-height: 1.2;
        transform: translateX(2ch);
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
        max-height: 350px; /* Reduziert */
        position: relative;
        z-index: 0;
    }

    /* Scroll-Icon */
    .custom-image-pos-1 {
        max-width: 40px; /* Kleiner */
        animation: bounce 2s infinite;
    }

    /* About-Links */
    .custom-about-links {
        background-color: #f9f9f9;
        padding: 8px 0; /* Kompakter */
    }

    .links-row {
        gap: 10px; /* Weniger Abstand */
    }

    .link-item {
        flex: 0 0 auto;
    }

    .custom-about-links .col-md-6 {
        max-width: 50%;
    }

    .about-links .custom-dropdown select {
    width: 100%;
    padding: 8px 32px 8px 12px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #FDD55C;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}

    /* Wishlist-Icon anpassen */
    .sub-link-item .wishlist-component img {
        width: 20px; /* Kleiner */
        height: 20px;
        background: none;
        border: none;
    }

    /* Animation */
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }


    /* Ergebnis-Text */
    .custom-results-text {
        background-color: #fdd55c;
        padding: 6px 15px; /* Kompakter */
        text-align: center;
        border-radius: 6px;
        font-weight: bold;
        font-size: 1rem; /* Kleiner */
        color: #333;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 10px; /* Weniger Abstand */
        display: inline-block;
    }

    .custom-text {
        margin: 0;
        line-height: 1.3; /* Kompakter */
    }

    /* Hamburger-Menü */
    .menu-toggle {
        display: none;
    }

    /* Responsive Anpassungen */
    @media (max-width: 992px) {
        .custom-header-section {
            padding: 1.5rem 0; /* Kompakter */
        }
        .custom-image-position-2 {
            max-height: 280px; /* Reduziert */
            position: static;
        }
    }

    @media (max-width: 767px) {
        .menu-toggle {
            display: block;
        }
        .links-row {
            flex-wrap: wrap;
            gap: 4px; /* Weniger Abstand */
            justify-content: flex-end;
        }
        #mobileMenu:not(.show) {
            display: none;
        }
        #mobileMenu.show {
            width: 100%;
            padding: 8px; /* Kompakter */
            background: #f9f9f9;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .link-item.col-md-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        .link-pair {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            gap: 4px;
            width: 100%;
        }
        .sub-link-item {
            flex: 1 1 48%;
            max-width: 48%;
        }
        .travel-heading-with-bg {
            font-size: 1rem; /* Kleiner */
            padding: 0.3rem 0.6rem;
            text-align: center;
        }
        .travel-destination {
            font-size: 1.8rem; /* Kleiner */
            padding: 0.3rem 0.6rem;
            transform: none;
            text-align: center;
        }
        .custom-image-position-2 {
            max-height: 220px; /* Reduziert */
            position: static;
        }
        .custom-image-pos-1 {
            display: none;
        }
        .custom-header-section {
            padding: 1rem 0; /* Kompakter */
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
            padding-top: 150px !important; /* Reduziert */
            padding-bottom: 50px !important; /* Reduziert */
        }
        .custom-image-position-2 {
            position: relative;
            max-width: 100%;
            top: -50px; /* Weniger Überlappung */
            z-index: 0;
            height: 350px; /* Reduziert */
        }
    }

    @media (max-width: 576px) {
        .custom-header-section {
            padding: 0.8rem 0; /* Noch kompakter */
        }
        .travel-heading-with-bg {
            font-size: 0.9rem;
        }
        .travel-destination {
            font-size: 1.4rem;
        }
        .custom-image-position-2 {
            max-height: 180px; /* Reduziert */
        }
        .custom-about-links {
            padding: 4px 0; /* Kompakter */
        }
        .links-row {
            gap: 6px;
        }
    }

/* Baseline-Ausrichtung für alle Textelemente */
.links-row, .link-pair, .sub-link-item {
    align-items: baseline;
}

/* Konsistente Line-Height für Textelemente */
.sub-link-item {
    line-height: 1.5;
}

/* Responsive Anpassungen */
@media (max-width: 767.98px) {
    #mobileMenu {
        align-items: baseline;
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
            const maxScroll = sectionHeight;
            const translateY = Math.min(scrollPosition * 0.3, maxScroll); /* Sanfterer Effekt */
            parallax.style.transform = 'translateY(-' + translateY + 'px)';
        }
    });
</script>
<style>
.sticky-nav-wrapper {
    position: static;
    width: 100%;
    background: #e2e8f0; /* Helles Grau, anpassbar */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
    z-index: 22;
}

.sticky-nav-wrapper.fixed {
    position: fixed;
    top: 0;
    left: 0;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.sticky-nav {
    max-width: 1140px;
    margin: 0 auto;
    padding: 10px 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.logo-container {
    flex-shrink: 0;
}

.nav-logo {
    height: 40px;
    width: auto;
    transition: height 0.3s ease;
}

.sticky-nav-list {
    display: flex;
    justify-content: flex-end;
    gap: 25px;
    list-style: none;
    margin: 0;
    padding: 0;
    flex-wrap: nowrap;
}

.sticky-nav-list li a {
    text-decoration: none;
    color: #333; /* Dunkler Text für Kontrast auf Grau */
    font-weight: 600;
    font-size: 1rem;
    padding: 8px 12px;
    border-radius: 25px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    display: inline-block;
}

.sticky-nav-list li a:hover,
.sticky-nav-list li a.active {
    color: #fff; /* Weiß beim Hover für besseren Kontrast */
    background: #4a5568; /* Dunkleres Grau für Hover-Effekt */
    transform: translateY(-2px);
}

.sticky-nav-list li a::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: #4a5568; /* Dunkleres Grau für Unterstrich */
    transform: scaleX(0);
    transform-origin: bottom right;
    transition: transform 0.3s ease-out;
}

.sticky-nav-list li a:hover::after,
.sticky-nav-list li a.active::after {
    transform: scaleX(1);
    transform-origin: bottom left;
}

/* Responsive Anpassungen */
@media (max-width: 992px) {
    .sticky-nav-list {
        gap: 15px;
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 5px;
    }

    .sticky-nav-list li a {
        font-size: 0.95rem;
        padding: 6px 10px;
    }

    .nav-logo {
        height: 35px;
    }
}

@media (max-width: 768px) {
    .sticky-nav {
        padding: 8px 10px;
        flex-wrap: wrap;
    }

    .sticky-nav-list {
        gap: 10px;
        justify-content: flex-start;
    }

    .sticky-nav-list li a {
        font-size: 0.9rem;
        padding: 6px 8px;
    }

    .nav-logo {
        height: 30px;
    }
}

@media (max-width: 576px) {
    .sticky-nav {
        padding: 6px 8px;
    }

    .sticky-nav-list {
        gap: 8px;
    }

    .sticky-nav-list li a {
        font-size: 0.85rem;
        padding: 5px 7px;
    }

    .nav-logo {
        height: 25px;
    }
}

/* Platzhalter für darunterliegenden Inhalt, wenn fixiert */
body.fixed-nav {
    padding-top: 60px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const stickyNav = document.querySelector('.sticky-nav-wrapper');
    const sections = document.querySelectorAll('section');
    const navLinks = document.querySelectorAll('.sticky-nav-list a');
    let navHeight = stickyNav.offsetHeight; // Höhe der Menüleiste dynamisch ermitteln
    let navOffsetTop = stickyNav.offsetTop; // Ursprüngliche Position im Dokument

    window.addEventListener('scroll', () => {
        // Fixieren, wenn die ursprüngliche Position überschritten wird
        if (window.scrollY > navOffsetTop) {
            stickyNav.classList.add('fixed');
            document.body.classList.add('fixed-nav'); // Platzhalter für Inhalt
        } else {
            stickyNav.classList.remove('fixed');
            document.body.classList.remove('fixed-nav');
        }

        // Aktiver Link
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop - (navHeight + 60); // Offset für bessere Sichtbarkeit
            if (pageYOffset >= sectionTop) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(a => {
            a.classList.remove('active');
            if (a.getAttribute('href').includes(current)) {
                a.classList.add('active');
            }
        });
    });
});
    </script>