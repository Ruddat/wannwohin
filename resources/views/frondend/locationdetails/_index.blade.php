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
                                    <h4 class="timeline-title">@autotranslate("Flug: {$location->title}", app()->getLocale())</h4>
                                    <script async src="https://tp.media/content?currency=eur&trs=394771&shmarker=611711&lat=&lng=&powered_by=true&search_host=www.aviasales.at%2Fsearch&locale=de&origin=LON&value_min=0&value_max=1000000&round_trip=true&only_direct=false&radius=1&draggable=true&disable_zoom=false&show_logo=false&scrollwheel=true&primary=%233FABDB&secondary=%233FABDB&light=%23ffffff&width=1500&height=500&zoom=2&promo_id=4054&campaign_id=100" charset="utf-8"></script>
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






@endsection
