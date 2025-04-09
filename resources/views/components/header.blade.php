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
            padding: 2rem 0;
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
            font-size: clamp(1rem, 3vw, 1.3rem);
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
            font-size: clamp(1.8rem, 4vw, 2.5rem);
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
            max-height: 350px;
        }

        .custom-image-position-2 {
            border: 3px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 0;
        }

        /* Scroll-Icon */
        .custom-image-pos-1 {
            max-width: 40px;
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

        .header-search {
            flex: 0 0 auto;
        }

        .continent-selector {
            flex: 0 0 auto;
        }

        .wishlist-icon {
            flex: 0 0 auto;
        }

        /* About-Links */
        .custom-about-links {
            background-color: #f9f9f9;
            padding: 8px 0;
        }

        .links-row {
            gap: 10px;
        }

        /* Animation */
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        /* Ergebnis-Text */
        .custom-results-text {
            background-color: #fdd55c;
            padding: 6px 15px;
            text-align: center;
            border-radius: 6px;
            font-weight: bold;
            font-size: 1rem;
            color: #333;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
            display: inline-block;
        }

        /* Responsive Anpassungen */
        @media (max-width: 992px) {
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
            .custom-header-section {
                padding: 1rem 0;
            }

            .custom-image-position-2 {
                max-height: 200px;
            }

            .menu-toggle {
                display: block;
            }

            #mobileMenu:not(.show) {
                display: none;
            }

            #mobileMenu.show {
                width: 100%;
                padding: 8px;
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
                flex-wrap: nowrap;
                gap: 4px;
                width: 100%;
            }

            .sub-link-item {
                flex: 1 1 48%;
                max-width: 48%;
            }

            .sticky-nav-wrapper,
            .custom-image-position-2 {
                box-shadow: none;
            }
        }

        @media (min-width: 768px) {
            .custom-section-padding-1 {
                padding-top: 120px !important;
                padding-bottom: 40px !important;
            }
        }

        @media (max-width: 576px) {
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
        .bread-container {
    margin-top: 13px;
}

</style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Parallax-Effekt
            const parallax = document.querySelector('.parallax-background');
            const section = document.querySelector('.custom-header-section');
            if (parallax && section) {
                window.addEventListener('scroll', () => {
                    const scrollPosition = window.pageYOffset;
                    const translateY = Math.min(scrollPosition * 0.3, section.offsetHeight);
                    parallax.style.transform = 'translateY(-' + translateY + 'px)';
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
