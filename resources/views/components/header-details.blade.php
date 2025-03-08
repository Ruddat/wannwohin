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
                       {{--  @autotranslate('STÄDTEREISE NACH', app()->getLocale()) --}}
                        {{ $panoramaTitle ?? 'DEINE REISE NACH' }}
                    </h2>
                    <h1 class="travel-destination">
                        {{--  {!! app('autotranslate')->trans($headLine ?? 'Default Header Text', app()->getLocale()) !!} --}}
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

<div class="about-links bg-color-light">
    <div class="container">
        <div class="links-row d-flex justify-content-end align-items-baseline gap-3">
            <div class="menu-toggle d-md-none">
                <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="collapse d-md-flex justify-content-end align-items-baseline gap-3" id="mobileMenu">
                <div class="link-item col-md-6 col-12">
                    @livewire('frontend.header-search.header-search-component')
                </div>
                <div class="link-item link-pair d-flex flex-wrap align-items-end gap-2">
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
        background-attachment: fixed;
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

    /* About-Links */
    .about-links {
        background-color: #f9f9f9;
        padding: 10px 0;
    }

    .links-row {
        gap: 15px;
    }

    .link-item {
        flex: 0 0 auto;
    }

    .about-links .col-md-6 {
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

    /* Animation */
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    /* Fix für Firefox */
    @-moz-document url-prefix() {
        .parallax-background {
            background-attachment: scroll;
            transform: none;
        }
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
            display: none; /* Ausgeblendet, wenn nicht aktiv */
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
            flex-wrap: nowrap; /* Nebeneinander erzwingen */
            gap: 5px;
            width: 100%;
        }
        .sub-link-item {
            flex: 1 1 48%;
            max-width: 48%;
        }
    }

    @media (min-width: 768px) {
        .menu-toggle {
            display: none;
        }
        #mobileMenu {
            display: flex !important; /* Immer sichtbar ab 768px */
        }
        .link-pair {
            display: contents; /* Normales Verhalten auf Desktop */
        }
        .sub-link-item {
            flex: 0 0 auto;
        }
    }

    @media (max-width: 576px) {
        .header-section {
            padding: 1rem 0;
        }
        .header-section .travel-heading {
            font-size: 1rem;
        }
        .header-section .travel-destination {
            font-size: 1.5rem;
        }
        .header-section .header-main-img {
            max-height: 200px;
        }
        .about-links {
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
        let scrollPosition = window.pageYOffset;

        // Parallax-Geschwindigkeit
        parallax.style.transform = 'translateY(' + scrollPosition * 0.5 + 'px)';
    });
    </script>
