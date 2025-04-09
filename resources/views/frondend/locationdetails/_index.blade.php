@extends('layouts.main')

@section('content')

<div class="body" id="location_page">
    <div role="main" class="main">
        @include('frondend.locationdetails.sections.main')

        <!-- Bootstrap CSS (falls nicht bereits im Layout enthalten) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
        @include('frondend.locationdetails.sections.maps')
    </div>
</div>
<!-- Lage und Klima -->
@if ($location->text_what_to_do)
<div class="col-lg-6 col-md-12" data-aos="fade-up" data-aos-delay="300">
    <div class="card h-100 shadow-lg border-0">
        @include('frondend.locationdetails.sections.location-climate')
    </div>
</div>
@endif



{{--
<!-- Beste Reisezeit -->
<div class="col-lg-6 col-md-12" data-aos="fade-up" data-aos-delay="200">
    <div class="card h-100 shadow-lg border-0">
        @include('frondend.locationdetails.sections.best-travel')
    </div>
</div>
--}}


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


        @if ($location->text_what_to_do)
            @include('frondend.locationdetails.sections.erleben')
        @endif


        @include('frondend.locationdetails.sections.climate_table')

        {{--
        <!-- Inspirationsbereich -->
        @livewire('frontend.location-inspiration.location-inspiration-component', [
            'locationId'    => $location->id,
            'locationTitle' => $location->title
        ])
        --}}
        <!-- Aktivitäten -->
        @livewire('frontend.location-inspiration-component.trip-activities', [
            'locationId'    => $location->id,
            'locationTitle' => $location->title
        ])





        <!-- Zusätzliche Sektionen -->
        @include('frondend.locationdetails.sections.amusement_parks')

        @if ($gallery_images)
            @include('frondend.locationdetails.sections.erleben_picture_modal')
        @endif
    </div>
</div>


    <!-- Google Maps Modal -->
    <div class="modal fade" id="google_map_modal" tabindex="-1" aria-labelledby="googleMapModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="googleMapModalLabel">Position auf der Karte</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row" style="min-height: 400px">
                        <div class="col-12">
                            <div class="mapouter">
                                <div class="gmap_canvas">
                                    <iframe
                                        width="100%"
                                        height="400"
                                        id="gmap_canvas"
                                        src="https://maps.google.com/maps?q={{ urlencode($location->title) }}&t=&z=10&ie=UTF8&iwloc=&output=embed"
                                        frameborder="0"
                                        scrolling="no"
                                        marginheight="0"
                                        marginwidth="0"
                                    ></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>




{{--

<!-- AOS in npm-->
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

--}}



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



    /* Modal im Vordergrund (global definiert) */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1055; /* Über dem Backdrop und anderen Inhalten */
    }

    .modal-dialog {
        z-index: 1060; /* Noch höher für Modal-Inhalte */
        position: relative;
    }

    .modal-content {
        border-radius: 1rem;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        transform: scale(0.95);
        transition: transform 0.3s ease;
        background: white;
    }

    .modal.show .modal-content {
        transform: scale(1);
    }

    /* Sektionen tiefer halten */
    .section {
        position: relative;
       /*
        z-index: 1; /* Unter dem Modal */
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
