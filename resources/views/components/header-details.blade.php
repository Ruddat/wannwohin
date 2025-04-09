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

<section class="header-section section section-parallax bg-transparent m-0">
    <div class="parallax-background" style="background-image: url('{{ $pic1Text ?? asset('default-bg.jpg') }}');"></div>

    <div class="container">
        <div class="row align-items-center g-4">
            <!-- Hauptbild -->
            <div class="col-lg-4 col-md-5 col-sm-6 position-relative">
                <img src="{{ $pic3Text ?? asset('default-main.jpg') }}"
                     class="img-fluid header-main-img"
                     alt="Main Image">
            </div>

            <!-- Header-Text -->
            <div class="col-lg-6 col-xl-5 col-md-7 col-sm-6">
                <div class="heading-wrapper">
                    <h2 class="travel-heading">
                        {{ $panoramaTitle ?? 'DEINE REISE NACH' }}
                    </h2>
                    <h1 class="travel-destination">
                        {!! str_replace(['<p>', '</p>'], '', $panoramaShortText ?? '') !!}
                    </h1>
                </div>
            </div>

            <!-- Scroll-Icon -->
            <div class="col-lg-2 col-xl-3 d-none d-lg-block text-center">
                <img src="{{ asset('assets/img/pages/main/mouse.png') }}"
                     class="scroll-icon img-fluid"
                     alt="Scroll Icon">
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
            <button class="nav-toggle d-lg-none" aria-label="Menü öffnen">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="sticky-nav-list">
                <li><a href="#section-climate">Klima</a></li>
                <li><a href="#section-activities">Aktivitäten</a></li>
                <li><a href="#section-recommendations">Empfehlungen</a></li>
            </ul>
            <div class="nav-components">
                <div class="header-search">
                    @livewire('frontend.header-search.header-search-component')
                </div>
                <div class="continent-selector">
                    @livewire('frontend.continent-selector.continent-selector-component')
                </div>
                <div class="wishlist-icon">
                    @livewire('frontend.wishlist-select.wishlist-component')
                </div>
                <div class="wishlist-icon">
                    <livewire:frontend.location-inspiration-component.favorite-activities-indicator />
                </div>
            </div>
        </div>
    </div>

    <div class="bread-container">
        <x-breadcrumb />
    </div>
</div>

<style>
    /* Parallax-Effekt */
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

    /* Header-Sektion */
    .header-section {
        position: relative;
        overflow: hidden;
        padding: 5rem 0;
    }

    .header-section .heading-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0;
    }

    .header-section .travel-heading {
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

    .header-section .travel-destination {
        font-size: 2.3rem;
        font-weight: bold;
        text-transform: uppercase;
        color: #fff;
        background: rgba(0, 0, 0, 0.7);
        padding: 0.5rem 1rem;
        margin: 0;
        line-height: 1;
        transform: translateX(3ch);
    }

    .header-section .header-main-img {
        border: 3px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-height: 400px;
        object-fit: cover;
    }

    .header-section .scroll-icon {
        max-width: 50px;
        animation: bounce 2s infinite;
    }

    /* Sticky Navigation */
    .sticky-nav-wrapper {
        position: static;
        width: 100%;
        background: #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        z-index: 22;
    }

    .sticky-nav-wrapper.fixed {
        position: fixed;
        top: 0;
        left: 0;
        background: rgba(226, 232, 240, 0.85);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .sticky-nav {
        max-width: 1140px;
        margin: 0 auto;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
    }

    .nav-toggle {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #333;
        cursor: pointer;
        display: none;
    }

    .sticky-nav-list {
        display: flex;
        gap: 25px;
        list-style: none;
        margin: 0;
        padding: 0;
        transition: all 0.3s ease;
    }

    .sticky-nav-list li a {
        text-decoration: none;
        color: #333;
        font-weight: 600;
        font-size: 1rem;
        padding: 8px 12px;
        border-radius: 25px;
        transition: all 0.3s ease;
    }

    .sticky-nav-list li a:hover,
    .sticky-nav-list li a.active {
        color: #fff;
        background: #4a5568;
    }

    .nav-logo {
        height: 40px;
        width: auto;
        transition: height 0.3s ease;
    }

    .nav-components {
        display: flex;
        gap: 15px;
        align-items: flex-end;
    }

    .header-search,
    .continent-selector,
    .wishlist-icon {
        flex: 0 0 auto;
    }

    /* About-Links */
    .custom-about-links {
        background-color: #f9f9f9;
        padding: 8px 0;
    }

    .links-row {
        gap: 15px;
    }

    .link-item {
        flex: 0 0 auto;
    }

    /* Animation */
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    /* Hamburger-Menü */
    .menu-toggle {
        display: none;
    }

    /* Responsive Anpassungen */
    @media (max-width: 992px) {
        .header-section {
            padding: 2rem 0;
        }
        .header-section .header-main-img {
            max-height: 300px;
        }
        .nav-toggle {
            display: block;
        }
        .sticky-nav-list {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: rgba(226, 232, 240, 0.95);
            flex-direction: column;
            padding: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }
        .sticky-nav-list.active {
            display: flex;
        }
        .sticky-nav-list li {
            width: 100%;
        }
        .sticky-nav-list li a {
            display: block;
            padding: 10px;
            text-align: center;
        }
        .nav-components {
            gap: 10px;
            margin-left: 10px;
        }
        .header-search {
            display: none; /* Verstecke Suche im Dropdown, bleibt oben sichtbar */
        }
    }

    @media (max-width: 767px) {
        .header-section {
            padding: 1rem 0;
        }
        .header-section .header-main-img {
            max-height: 200px;
        }
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
            flex-wrap: nowrap;
            gap: 5px;
            width: 100%;
        }
        .sub-link-item {
            flex: 1 1 48%;
            max-width: 48%;
        }
        .sticky-nav-wrapper,
        .header-main-img {
            box-shadow: none;
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
    }

    @media (max-width: 576px) {
        .header-section .travel-heading {
            font-size: 1rem;
        }
        .header-section .travel-destination {
            font-size: 1.5rem;
        }
        .nav-logo {
            height: 25px;
        }
        .sticky-nav {
            padding: 8px 10px;
        }
        .nav-components {
            gap: 5px;
        }
    }

    body.fixed-nav {
        padding-top: 60px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Parallax-Effekt
        const parallax = document.querySelector('.parallax-background');
        if (parallax) {
            window.addEventListener('scroll', () => {
                const scrollPosition = window.pageYOffset;
                parallax.style.transform = 'translateY(' + scrollPosition * 0.5 + 'px)';
            });
        }

        // Sticky Navigation
        const stickyNav = document.querySelector('.sticky-nav-wrapper');
        const navToggle = document.querySelector('.nav-toggle');
        const navList = document.querySelector('.sticky-nav-list');
        let navOffsetTop = stickyNav.offsetTop;

        navToggle.addEventListener('click', () => {
            navList.classList.toggle('active');
        });

        window.addEventListener('scroll', () => {
            if (window.scrollY > navOffsetTop) {
                stickyNav.classList.add('fixed');
                document.body.classList.add('fixed-nav');
            } else {
                stickyNav.classList.remove('fixed');
                document.body.classList.remove('fixed-nav');
            }
        });
    });
</script>
