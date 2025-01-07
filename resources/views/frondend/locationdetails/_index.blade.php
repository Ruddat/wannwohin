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
                                <h4 class="timeline-title">Karte</h4>
                                @include('frondend.locationdetails.sections.maps')
                            </div>
                        </div>

                        <!-- Best Travel Section -->
                        <div class="timeline-item">
                            <div class="timeline-marker">2</div>
                            <div class="timeline-content">
                                <h4 class="timeline-title">Beste Reisezeit</h4>
                                @include('frondend.locationdetails.sections.best-travel')
                            </div>
                        </div>

                        <!-- Location Climate Section -->
                        <div class="timeline-item">
                            <div class="timeline-marker">3</div>
                            <div class="timeline-content">
                                <h4 class="timeline-title">Lage und Klima</h4>
                                @include('frondend.locationdetails.sections.location-climate')
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            @include('frondend.locationdetails.sections.climate_table')

            @include('frondend.locationdetails.sections.amusement_parks')

            @include('frondend.locationdetails.sections.erleben')

            @include('frondend.locationdetails.sections.erleben_picture_modal')
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
