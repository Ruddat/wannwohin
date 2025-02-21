@extends('layouts.main')

@section('content')
    <div class="body" id="location_page">


        <div role="main" class="main">
            @include('frondend.locationdetails.sections.main')

            <section id="experience" class="section section-secondary section-no-border m-0 pt-0">


                <div class="container mt-5 mb-5">
                    <div class="row">
                        <div class="col-md-12 offset-md-0">
                            <h4 class="mb-4">Latest News</h4>
                            <ul class="timeline">
                                <li>
                                    <h4 class="timeline-title">@autotranslate("Karte & Route: {$location->title}", app()->getLocale())</h4>
                                    @include('frondend.locationdetails.sections.maps')

                                    <a href="https://www.totoprayogo.com/#" target="_blank">New Web Design</a>
                                    <span class="date ms-auto d-block">21 March, 2014</span>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque scelerisque diam non nisi semper, et elementum lorem ornare...</p>
                                </li>
                                <li>
                                    <a href="#">21 000 Job Seekers</a>
                                    <span class="date ms-auto d-block">4 March, 2014</span>
                                    <p>Curabitur purus sem, malesuada eu luctus eget, suscipit sed turpis...</p>
                                </li>
                                <li>
                                    <a href="#">Awesome Employers</a>
                                    <span class="date ms-auto d-block">1 April, 2014</span>
                                    <p>Fusce ullamcorper ligula sit amet quam accumsan aliquet...</p>
                                </li>
                                <li>
                                    <a href="#">Awesome Employers</a>
                                    <span class="date ms-auto d-block">1 April, 2014</span>
                                    <p>Fusce ullamcorper ligula sit amet quam accumsan aliquet...</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>















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
                        @if ($location->text_best_traveltime)
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


    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12 offset-md-1">
                <div class="vertical-tab">
                    <!-- Navigation -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab1" data-bs-toggle="tab" href="#Section1" role="tab">Section 1</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab2" data-bs-toggle="tab" href="#Section2" role="tab">Section 2</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab3" data-bs-toggle="tab" href="#Section3" role="tab">Section 3</a>
                        </li>
                    </ul>

                    <!-- Inhalte der Tabs -->
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="Section1" role="tabpanel">
                            <h3>@autotranslate("Karte & Route: {$location->title}", app()->getLocale())</h3>
                            @include('frondend.locationdetails.sections.maps')

                        </div>
                        <div class="tab-pane fade" id="Section2" role="tabpanel">
                            <h3>@autotranslate("Beste Reisezeit: {$location->title}", app()->getLocale())</h3>
                                @include('frondend.locationdetails.sections.best-travel')


                        </div>
                        <div class="tab-pane fade" id="Section3" role="tabpanel">
                            <h3>Section 3</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce semper, magna a ultricies volutpat.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        a:hover, a:focus {
            text-decoration: none;
            outline: none;
        }

        .vertical-tab {
            display: flex;
            align-items: flex-start;
        }

        .vertical-tab .nav-tabs {
            flex-direction: column;
            width: 30%;
            border: none;
        }

        .vertical-tab .nav-tabs .nav-link {
            background: #666;
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 10px;
            transition: all 0.3s ease-in-out;
        }

        .vertical-tab .nav-tabs .nav-link.active {
            background: #f44678;
            color: #fff;
        }

        .vertical-tab .tab-content {
            flex-grow: 1;
            background-color: #666;
            color: #fff;
            padding: 20px;
            border-radius: 5px;
        }

        .vertical-tab .tab-content h3 {
            color: #fff;
            font-size: 20px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        @media only screen and (max-width: 768px) {
            .vertical-tab {
                flex-direction: column;
            }

            .vertical-tab .nav-tabs {
                width: 100%;
            }

            .vertical-tab .nav-tabs .nav-link {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h4 class="mb-4">Latest News</h4>
            <ul class="timeline">
                <li>
                    <a href="https://www.totoprayogo.com/#" target="_blank">New Web Design</a>
                    <span class="date ms-auto d-block">21 March, 2014</span>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque scelerisque diam non nisi semper, et elementum lorem ornare...</p>
                </li>
                <li>
                    <a href="#">21 000 Job Seekers</a>
                    <span class="date ms-auto d-block">4 March, 2014</span>
                    <p>Curabitur purus sem, malesuada eu luctus eget, suscipit sed turpis...</p>
                </li>
                <li>
                    <a href="#">Awesome Employers</a>
                    <span class="date ms-auto d-block">1 April, 2014</span>
                    <p>Fusce ullamcorper ligula sit amet quam accumsan aliquet...</p>
                </li>
                <li>
                    <a href="#">Awesome Employers</a>
                    <span class="date ms-auto d-block">1 April, 2014</span>
                    <p>Fusce ullamcorper ligula sit amet quam accumsan aliquet...</p>
                </li>
            </ul>
        </div>
    </div>
</div>


<style>
    ul.timeline {
    list-style-type: none;
    position: relative;
}
ul.timeline:before {
    content: ' ';
    background: #d4d9df;
    display: inline-block;
    position: absolute;
    left: 29px;
    width: 2px;
    height: 100%;
    z-index: 400;
}
ul.timeline > li {
    margin: 20px 0;
    padding-left: 60px;
}
ul.timeline > li:before {
    content: ' ';
    background: white;
    display: inline-block;
    position: absolute;
    border-radius: 50%;
    border: 3px solid #22c0e8;
    left: 20px;
    width: 20px;
    height: 20px;
    z-index: 400;
}
</style>

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
