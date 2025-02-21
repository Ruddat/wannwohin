@extends('layouts.main')

@section('content')
    <div class="body" id="location_page">

        <div role="main" class="main">
            @include('frondend.locationdetails.sections.main')

            <section id="experience" class="section section-secondary section-no-border m-0 pt-0">
                <div class="container mt-2 mb-2">
                    <div class="row">
                        <div class="col-md-12 offset-md-0">
                            <ul class="timeline">
                                <li>
                                    <h4 class="timeline-title">@autotranslate("Karte & Route: {$location->title}", app()->getLocale())</h4>
                                    @include('frondend.locationdetails.sections.maps')
                                </li>
                                <li>
                                    <h4 class="timeline-title">@autotranslate("Beste Reisezeit: {$location->title}", app()->getLocale())</h4>
                                    @include('frondend.locationdetails.sections.best-travel')
                                </li>

                                @if ($location->text_what_to_do)
                                    <li>
                                        <h4 class="timeline-title">@autotranslate("Lage und Klima: {$location->title}", app()->getLocale())</h4>
                                        @include('frondend.locationdetails.sections.location-climate')
                                    </li>
                                @endif

                                @if ($location->text_sports)
                                    <li>
                                        <h4 class="timeline-title">@autotranslate("Sport & Aktivitäten: {$location->title}", app()->getLocale())</h4>
                                        <div class="container my-4">
                                            <article class="timeline-box right custom-box-shadow-2 box-shadow-2">
                                                <div class="row">
                                                    <div class="experience-description col-lg-12 col-sm-6 bg-color-light px-4 py-3 rounded-end">
                                                        <h4 class="text-color-dark font-weight-semibold">
                                                            <i class="fa-solid fa-dumbbell me-2 text-primary"></i>
                                                            @autotranslate("Sport & Aktivitäten in {$location->title}", app()->getLocale())
                                                        </h4>
                                                        <div class="formatted-text">
                                                            {!! app('autotranslate')->trans($location->text_sports, app()->getLocale()) !!}
                                                        </div>
                                                        <div class="d-flex flex-wrap mt-3">
                                                            @foreach (['⚽ Fußball', '🏀 Basketball', '🏎️ Motorsport', '🚴‍♂️ Radfahren', '🎿 Wintersport', '🏊‍♂️ Wassersport'] as $sport)
                                                                <span class="badge bg-primary text-white me-2 mb-2">{{ $sport }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </article>
                                        </div>
                                    </li>
                                @endif

                                @if ($location->text_amusement_parks)
                                    <li>
                                        <h4 class="timeline-title">@autotranslate("Freizeitparks & Attraktionen: {$location->title}", app()->getLocale())</h4>
                                        <div class="container my-4">
                                            <article class="timeline-box right custom-box-shadow-2 box-shadow-2">
                                                <div class="row">
                                                    <div class="experience-description col-lg-12 col-sm-6 bg-color-light px-4 py-3 rounded-end">
                                                        <h4 class="text-color-dark font-weight-semibold">
                                                            <i class="fa-solid fa-ticket-alt me-2 text-success"></i>
                                                            @autotranslate("Freizeitparks & Attraktionen in {$location->title}", app()->getLocale())
                                                        </h4>
                                                        <div class="formatted-text">
                                                            {!! app('autotranslate')->trans($location->text_amusement_parks, app()->getLocale()) !!}
                                                        </div>
                                                        <div class="d-flex flex-wrap mt-3">
                                                            @foreach (['🎢 Achterbahnen', '🎡 Riesenrad', '🎠 Karussells', '🎭 Shows & Events', '🍔 Freizeitpark-Gastronomie'] as $attraction)
                                                                <span class="badge bg-success text-white me-2 mb-2">{{ $attraction }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </article>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
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
ul.timeline {
    list-style-type: none;
    position: relative;
    padding: 0;
    margin: 0;
}

/* Linie links platzieren */
ul.timeline:before {
    content: '';
    background: #d4d9df;
    display: inline-block;
    position: absolute;
    left: 30px; /* Linie links positionieren */
    width: 2px;
    height: 100%;
    z-index: 400;
}

/* Eintrag-Elemente */
ul.timeline > li {
    margin: 50px 0;
    padding-left: 60px; /* Abstand zur Linie */
    position: relative;
}

/* Punkte mittig auf der Linie */
ul.timeline > li:before {
    content: '';
    background: white;
    display: inline-block;
    position: absolute;
    top: 50%; /* Punkt mittig platzieren */
    transform: translateY(-50%);
    left: 21px; /* Abstand zur Linie */
    border-radius: 50%;
    border: 3px solid #22c0e8;
    width: 20px;
    height: 20px;
    z-index: 401;
    transition: background 0.3s ease;
}

/* Hover-Effekt für Punkte */
ul.timeline > li:hover:before {
    background: #22c0e8;
}

/* Inhalt der Timeline-Boxen */
.timeline-content {
    background: #fff;
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease-in-out;
    text-align: left;
}

/* Hover-Effekt für die Boxen */
ul.timeline > li:hover .timeline-content {
    transform: translateY(-5px);
}

/* Responsive Anpassung */
@media (max-width: 768px) {
    ul.timeline {
        padding-left: 20px;
    }

    ul.timeline:before {
        left: 15px;
    }

    ul.timeline > li:before {
        left: 10px;
    }
}

    </style>



@endsection
