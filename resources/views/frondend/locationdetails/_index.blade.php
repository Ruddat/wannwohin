@extends('layouts.main')

@section('content')
    <div class="body" id="location_page">


        <div role="main" class="main">
            @include('frondend.locationdetails.sections.main')

            <section id="experience" class="section section-secondary section-no-border m-0 pt-0">
                <div class="container">
                    <div class="timeline">
                        <!-- Maps Section -->
                        <div class="timeline-item">
                            <div class="timeline-marker">1</div>
                            <div class="timeline-content">
                                <h4 class="timeline-title">@autotranslate("Karte & Route: {$location->title}", app()->getLocale())</h4>
                                @include('frondend.locationdetails.sections.maps')
                            </div>
                        </div>

                        <!-- Best Travel Section -->
                        @if ($location->text_what_to_do)
                        <div class="timeline-item">
                            <div class="timeline-marker">2</div>
                            <div class="timeline-content">
                                <h4 class="timeline-title">@autotranslate("Beste Reisezeit: {$location->title}", app()->getLocale())</h4>
                                @include('frondend.locationdetails.sections.best-travel')
                            </div>
                        </div>
                        @endif

                        @if ($location->text_sports)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary text-white">⚽</div>
                            <div class="timeline-content border rounded shadow-sm p-4">
                                <h4 class="timeline-title d-flex align-items-center">
                                    <i class="fa-solid fa-dumbbell me-2 text-primary"></i>
                                    @autotranslate("Sport & Aktivitäten in {$location->title}", app()->getLocale())
                                </h4>
                                <div class="text-muted">
                                    {!! $location->text_sports !!}
                                </div>
                                <div class="d-flex flex-wrap mt-3">
                                    @foreach(['⚽ Fußball', '🏀 Basketball', '🏎️ Motorsport', '🚴‍♂️ Radfahren', '🎿 Wintersport', '🏊‍♂️ Wassersport'] as $sport)
                                        <span class="badge bg-primary text-white me-2 mb-2">{{ $sport }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif


                        @if ($location->text_amusement_parks)
<div class="timeline-item">
    <div class="timeline-marker bg-success text-white">🎡</div>
    <div class="timeline-content border rounded shadow-sm p-4">
        <h4 class="timeline-title d-flex align-items-center">
            <i class="fa-solid fa-ticket-alt me-2 text-success"></i>
            @autotranslate("Freizeitparks & Attraktionen in {$location->title}", app()->getLocale())
        </h4>
        <div class="text-muted">
            {!! $location->text_amusement_parks !!}
        </div>
        <div class="d-flex flex-wrap mt-3">
            @foreach(['🎢 Achterbahnen', '🎡 Riesenrad', '🎠 Karussells', '🎭 Shows & Events', '🍔 Freizeitpark-Gastronomie'] as $attraction)
                <span class="badge bg-success text-white me-2 mb-2">{{ $attraction }}</span>
            @endforeach
        </div>
    </div>
</div>
@endif


                        <!-- Location Climate Section -->
                        @if ($location->text_what_to_do)
                        <div class="timeline-item">
                            <div class="timeline-marker">3</div>
                            <div class="timeline-content">
                                <h4 class="timeline-title">@autotranslate("Lage und Klima: {$location->title}", app()->getLocale())</h4>
                                @include('frondend.locationdetails.sections.location-climate')
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </section>

            @include('frondend.locationdetails.sections.climate_table')

            @include('frondend.locationdetails.sections.amusement_parks')

            @if ($location->text_what_to_do)
            @include('frondend.locationdetails.sections.erleben')
            @endif
            @if ($gallery_images)
            @include('frondend.locationdetails.sections.erleben_picture_modal')
            @endif
        </div>
    </div>

    <style>
        /* Grundstruktur der Timeline */
        .timeline {
            position: relative;
            margin: 40px 0;
            padding: 0;
        }

        .timeline::before {
            content: "";
            position: absolute;
            top: 0;
            left: 20px;
            width: 4px;
            height: 100%;
            background: #007bff;
            border-radius: 2px;
        }

        .timeline-item {
            position: relative;
            margin: 40px 0;
            padding-left: 60px;
        }

        .timeline-marker {
            position: absolute;
            left: 5px;
            width: 36px;
            height: 36px;
            background: #007bff;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            line-height: 36px;
            border-radius: 50%;
            z-index: 2;
        }

        .timeline-content {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .timeline-title {
            margin-bottom: 10px;
            color: #007bff;
            font-size: 20px;
            font-weight: bold;
        }

        /* Marker zentriert ausrichten */
        .timeline-item .timeline-marker {
            top: 50%; /* Standardwert */
            transform: translateY(-50%);
        }

        /* Responsive Anpassung */
        @media (max-width: 768px) {
            .timeline {
                margin: 20px 0;
            }

            .timeline-item {
                padding-left: 50px;
            }

            .timeline-marker {
                width: 30px;
                height: 30px;
                font-size: 14px;
                line-height: 30px;
                left: 5px;
            }

            .timeline-content {
                padding: 15px;
            }

            .timeline-title {
                font-size: 18px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
    const timelineItems = document.querySelectorAll('.timeline-item');

    timelineItems.forEach(item => {
        const content = item.querySelector('.timeline-content');
        const marker = item.querySelector('.timeline-marker');
        const contentHeight = content.offsetHeight;

        // Setze den Marker dynamisch auf die Mitte
        marker.style.top = `${contentHeight / 2}px`;
    });
});
    </script>
@endsection
