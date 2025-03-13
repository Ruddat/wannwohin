@extends('layouts.main')

@section('content')

<div class="body" id="location_page">
    <div role="main" class="main">
        @include('frondend.locationdetails.sections.main')

        <!-- Bootstrap CSS (falls nicht bereits im Layout enthalten) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Inspirationsbereich -->
        @livewire('frontend.location-inspiration.location-inspiration-component', [
            'locationId'    => $location->id,
            'locationTitle' => $location->title
        ])

        <!-- Erlebnis-Sektion -->
        <section id="experience" class="section section-no-border bg-light m-0 py-5 position-relative">
            <div class="parallax-bg" style="background-image: url('https://www.transparenttextures.com/patterns/paper-fibers.png');"></div>
            <div class="container position-relative z-index-2">
                <div class="row mb-4">
                    <div class="col-12 text-center">
                        <h2 class="text-color-dark fw-bold text-uppercase animate__animated animate__fadeInDown">
                            @autotranslate("Entdecken Sie {$location->title}", app()->getLocale())
                        </h2>
                        <hr class="w-25 mx-auto" style="border: 3px solid #ffd700;">
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Karte & Route -->
                    <div class="col-lg-6 col-md-12" data-aos="fade-up">
                        <div class="card h-100 shadow-lg border-0">
                            <div class="card-body p-4">
                                <h4 class="card-title text-primary fw-bold mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    @autotranslate("Karte & Route", app()->getLocale())
                                </h4>
                                @include('frondend.locationdetails.sections.maps')
                            </div>
                        </div>
                    </div>

                    <!-- Flug -->
                    <div class="col-lg-6 col-md-12" data-aos="fade-up" data-aos-delay="100">
                        <div class="card h-100 shadow-lg border-0">
                            <div class="card-body p-4">
                                <h4 class="card-title text-primary fw-bold mb-3">
                                    <i class="fas fa-plane me-2"></i>
                                    @autotranslate("Flug", app()->getLocale())
                                </h4>
                                <script async src="https://tp.media/content?currency=eur&trs=394771&shmarker=611711&lat=&lng=&powered_by=true&search_host=www.aviasales.at%2Fsearch&locale=de&origin=LON&value_min=0&value_max=1000000&round_trip=true&only_direct=false&radius=1&draggable=true&disable_zoom=false&show_logo=false&scrollwheel=true&primary=%233FABDB&secondary=%233FABDB&light=%23ffffff&width=1500&height=500&zoom=2&promo_id=4054&campaign_id=100" charset="utf-8"></script>
                            </div>
                        </div>
                    </div>

                    <!-- Beste Reisezeit -->
                    <div class="col-lg-6 col-md-12" data-aos="fade-up" data-aos-delay="200">
                        <div class="card h-100 shadow-lg border-0">
                            <div class="card-body p-4">
                                <h4 class="card-title text-primary fw-bold mb-3">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    @autotranslate("Beste Reisezeit", app()->getLocale())
                                </h4>
                                @include('frondend.locationdetails.sections.best-travel')
                            </div>
                        </div>
                    </div>

                    <!-- Lage und Klima -->
                    @if ($location->text_what_to_do)
                        <div class="col-lg-6 col-md-12" data-aos="fade-up" data-aos-delay="300">
                            <div class="card h-100 shadow-lg border-0">
                                <div class="card-body p-4">
                                    <h4 class="card-title text-primary fw-bold mb-3">
                                        <i class="fas fa-cloud-sun me-2"></i>
                                        @autotranslate("Lage und Klima", app()->getLocale())
                                    </h4>
                                    @include('frondend.locationdetails.sections.location-climate')
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Sport & Aktivitäten -->
                    @if ($location->text_sports)
                        <div class="col-lg-6 col-md-12" data-aos="fade-up" data-aos-delay="400">
                            <div class="card h-100 shadow-lg border-0">
                                <div class="card-body p-4">
                                    <h4 class="card-title text-primary fw-bold mb-3">
                                        <i class="fas fa-dumbbell me-2"></i>
                                        @autotranslate("Sport & Aktivitäten", app()->getLocale())
                                    </h4>
                                    <div class="formatted-text">
                                        {!! app('autotranslate')->trans($location->text_sports, app()->getLocale()) !!}
                                    </div>
                                    <div class="d-flex flex-wrap mt-3 gap-2">
                                        @foreach (['⚽ Fußball', '🏀 Basketball', '🏎️ Motorsport', '🚴‍♂️ Radfahren', '🎿 Wintersport', '🏊‍♂️ Wassersport'] as $sport)
                                            <span class="badge bg-primary text-white">{{ $sport }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Freizeitparks & Attraktionen -->
                    @if ($location->text_amusement_parks)
                        <div class="col-lg-6 col-md-12" data-aos="fade-up" data-aos-delay="500">
                            <div class="card h-100 shadow-lg border-0">
                                <div class="card-body p-4">
                                    <h4 class="card-title text-primary fw-bold mb-3">
                                        <i class="fas fa-ticket-alt me-2"></i>
                                        @autotranslate("Freizeitparks & Attraktionen", app()->getLocale())
                                    </h4>
                                    <div class="formatted-text">
                                        {!! app('autotranslate')->trans($location->text_amusement_parks, app()->getLocale()) !!}
                                    </div>
                                    <div class="d-flex flex-wrap mt-3 gap-2">
                                        @foreach (['🎢 Achterbahnen', '🎡 Riesenrad', '🎠 Karussells', '🎭 Shows & Events', '🍔 Freizeitpark-Gastronomie'] as $attraction)
                                            <span class="badge bg-success text-white">{{ $attraction }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Zusätzliche Sektionen -->
        @include('frondend.locationdetails.sections.amusement_parks')

        @include('frondend.locationdetails.sections.climate_table')

        @if ($location->text_what_to_do)
            @include('frondend.locationdetails.sections.erleben')
        @endif
        @if ($gallery_images)
            @include('frondend.locationdetails.sections.erleben_picture_modal')
        @endif
    </div>
</div>


<!-- AOS -->
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        AOS.init({
            duration: 800,
            once: true,
        });
    });
</script>

<style>
    .bg-light {
        background-color: #f8f9fa !important;
    }

    .parallax-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        opacity: 0.5;
        z-index: 1;
    }

    .z-index-2 {
        z-index: 2;
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: #fff;
        border-radius: 10px;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
    }

    .card-title {
        font-size: 1.25rem;
        color: #3fabdb;
    }

    .formatted-text {
        font-size: 1rem;
        line-height: 1.6;
        color: #333;
    }

    .badge {
        font-size: 0.9rem;
        padding: 0.5em 1em;
        border-radius: 20px;
    }

    @media (max-width: 768px) {
        .card {
            margin-bottom: 1.5rem;
        }

        .card-body {
            padding: 1.5rem !important;
        }

        .card-title {
            font-size: 1.1rem;
        }

        .formatted-text {
            font-size: 0.95rem;
        }

        .badge {
            font-size: 0.8rem;
        }
    }
</style>

@endsection
