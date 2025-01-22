@extends('layouts.main')
@section('content')
    <div role="main" class="main">
        <section id="experience" class="section section-secondary section-no-border m-0 pb-0 bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="float-end d-inline-flex align-items-center">
                            <span id="sort_result_direction" data-sort-direction="{{ request()->get('sort_direction', 'asc') }}" class="sort-direction pe-2 d-inline-flex flex-column cursor-click">
                                <i id="sort_result_up" class="fas fa-sort-up fa-lg {{ request()->get('sort_direction') === 'desc' ? 'fa-disabled' : '' }}" style="line-height: 3px"></i>
                                <i id="sort_result_down" class="fas fa-sort-down fa-lg {{ request()->get('sort_direction') === 'asc' ? 'fa-disabled' : '' }}" style="line-height: 3px"></i>
                            </span>
                            <label for="search_result_sort" class="pe-1 text-4">Sortieren: </label>
                            <select class="form-select" id="search_result_sort" name="search_result_sort">
                                <option value="price" {{ request()->get('sort_by') === 'price' ? 'selected' : '' }}>Preis</option>
                                <option value="location" {{ request()->get('sort_by') === 'location' ? 'selected' : '' }}>Reiseziel</option>
                                <option value="temperature" {{ request()->get('sort_by') === 'temperature' ? 'selected' : '' }}>Tagestemperatur</option>
                                <option value="continents" {{ request()->get('sort_by') === 'continents' ? 'selected' : '' }}>Kontinent</option>
                                <option value="countries" {{ request()->get('sort_by') === 'countries' ? 'selected' : '' }}>Land</option>
                                <option value="flight_hours" {{ request()->get('sort_by') === 'flight_hours' ? 'selected' : '' }}>Flugdauer</option>
                            </select>
                        </div>

                        <section class="timeline custom-timeline" id="timeline">
                            <div class="timeline-body">
                                @forelse($locations as $location)
                                    <article class="timeline-box right custom-box-shadow-2">
                                        <div class="row">
                                            @php
                                            //    dd($location);
                                            @endphp
                                            <!-- Image Section -->
                                            <div class="experience-info col-lg-3 col-sm-5 bg-color-primary p-0 m-0 overflow-hidden">
                                                <a href="{{ route('location.details', [
                                                    'continent' => $location->country->continent->alias,
                                                    'country' => $location->country->alias,
                                                    'location' => $location->alias,
                                                ]) }}" class="p-0 m-0">
                                                    <div class="my-zoom" style="background-image: url('{{ asset("{$location->text_pic1}") }}')">
                                                    </div>
                                                </a>
                                            </div>

                                            <!-- Content Section -->
                                            <div class="experience-description col-lg-9 col-sm-7 bg-color-light">

                                                <div class="row">
                                                    <div class="col-12 col-md-4">
                                                        <a href="{{ route('location.details', [
                                                'continent' => $location->country->continent->alias,
                                                'country' => $location->country->alias,
                                                'location' => $location->alias,
                                            ]) }}">
                                            <h4 class="text-7 text-dark mb-4">{{ $location->title }}</h4></a>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-3">
                                                        <div class="d-flex justify-content-start align-items-start">
                                                            <h5 class="text-5 text-dark d-block mb-4 me-2"> im Januar </h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <h5 class="text-7 text-dark mb-4 text-md-end">ab {{ number_format($location->price_flight, 0, ',', '.') }} €</h5>
                                                    </div>
                                                </div>

                                                <div class="row my-3 pb-1">
                                                    <div class="col-12 col-md-4 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            <img src="{{ asset('assets/flags/4x3/' . strtolower($location->country->country_code) . '.svg') }}"
                                                                 alt="{{ $location->country->title ?? 'Unknown Country' }}"
                                                                 class="me-4"
                                                                 style="width: 30px; height: auto;">
                                                            {{ $location->country->title ?? 'Unknown Country' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-md-3 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            <div class="col-6">Tagsüber</div>
                                                            <div class="col-4">{{ intval($location->climate_data['main']['temp'] ?? 'N/A') }}℃</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-5 d-flex align-items-end justify-content-start">
                                                        @php
                                                        $iconMap = [
                                                            'list_sports' => '<i class="fas fa-biking fa-lg me-1" title="Sport"></i>',
                                                            'list_island' => '<img style="margin-top: -3px;height: 30px;" src="' . asset('img/insel-icon.png') . '" alt="Insel" title="Insel">',
                                                            'list_culture' => '<i class="fa fa-theater-masks fa-lg me-1" title="Kultur"></i>',
                                                            'list_nature' => '<i class="fas fa-tree fa-lg me-1" title="Natur"></i>',
                                                            'list_watersport' => '<i class="fas fa-swimmer fa-lg me-1" title="Wassersport"></i>',
                                                            'list_wintersport' => '<i class="fas fa-snowflake fa-lg me-1" title="Wintersport"></i>',
                                                            'list_mountainsport' => '<i class="fas fa-mountain fa-lg me-1" title="Bergsport"></i>',
                                                            'list_biking' => '<i class="fas fa-biking fa-lg me-1" title="Radfahren"></i>',
                                                            'list_fishing' => '<i class="fas fa-fish fa-lg me-1" title="Angeln"></i>',
                                                            'list_amusement_park' => '<i class="fas fa-ticket-alt fa-lg me-1" title="Freizeitpark"></i>',
                                                            'list_water_park' => '<i class="fas fa-water fa-lg me-1" title="Wasserpark"></i>',
                                                            'list_animal_park' => '<i class="fas fa-paw fa-lg me-1" title="Tierpark"></i>',
                                                        ];
                                                        @endphp
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            @foreach($iconMap as $flag => $icon)
                                                            @if($location->$flag)
                                                            {!! $icon !!}
                                                            @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row my-3">
                                                    <div class="col-12 col-md-4 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            {{-- Kontinent-Flagge --}}
                                                            @if($location->country && $location->country->continent)
                                                                <img src="{{ asset('assets/img/location_main_img/' . strtolower($location->country->continent->alias) . '.png') }}"
                                                                     alt="{{ $location->country->continent->title }}"
                                                                     class="me-3"
                                                                     style="width: 30px; height: auto;">
                                                                {{ $location->country->continent->title ?? 'Unknown Continent' }}
                                                            @else
                                                                Unknown Continent
                                                            @endif
                                                        </div>
                                                    </div>


                                                    <div class="col-12 col-md-3 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            <div class="col-6">Wasser</div>
                                                            <div class="col-4">{{ intval($location->climate_data['water_temperature'] ?? 'N/A') }}℃</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-5 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            <div class="col-5">Regentage</div>
                                                            <div class="col-7">
                                                                {{ $location->climate_data['rain']['1h'] ?? 'N/A' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row my-3">
                                                    <div class="col-12 col-md-4 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            <i class="fas fa-arrows-alt-h text-6 me-3"></i>{{ number_format(round($location->flight_hours, 1), 1, ',', '.') }} Flugstunden
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            <div class="col-6">Sonne</div>
                                                            <div class="col-4">{{ $location->climate_data['sunshine_per_day'] ?? 'N/A' }} h</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-5 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            <div class="col-5">Beste Reisezeit</div>
                                                            <div class="col-7">{{ $location->best_traveltime ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Content Section End -->
                                        </div>
                                    </article>
                                @empty
                                    <p class="text-center">Keine Ergebnisse gefunden.</p>
                                @endforelse
                            </div>
                            <div class="timeline-bar"></div>
                        </section>
                    </div>
                </div>
            </div>
        </section>

        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    {{ $locations->appends(request()->query())->links() }}
                </div>
                <div class="font-weight-bold col-md-2 pt-2 px-0">
                    <div style="float: right">Ergebnisse pro Seite:</div>
                </div>
                <div class="col-md-1">
                    <select name="pagination" id="pagination" class="form-select d-inline">
                        <option value="10" @if($items_per_page == 10) selected @endif>10</option>
                        <option value="25" @if($items_per_page == 25) selected @endif>25</option>
                        <option value="50" @if($items_per_page == 50) selected @endif>50</option>
                    </select>
                </div>
            </div>
        </div>

        <script>
const pagination = document.getElementById('pagination');
if (pagination) {
    pagination.onchange = function () {
        const params = new URLSearchParams(window.location.search);
        params.set('items_per_page', this.value);
        window.location.search = params.toString();
    };
}

const sortSelect = document.getElementById('search_result_sort');
if (sortSelect) {
    sortSelect.addEventListener('change', async function () {
        const params = new URLSearchParams(window.location.search);
        params.set('sort_by', this.value);

        const response = await fetch(`/update-results?${params.toString()}`);
        const html = await response.text();

        document.getElementById('timeline').innerHTML = html;
    });
}
        </script>
    </div>


    <style>

 .my-zoom {
    aspect-ratio: 16 / 9;
    background-size: cover; /* Bild deckt den Container ab */
    background-position: center; /* Bild wird zentriert */
    width: 100%; /* Container breitet sich über die gesamte Breite aus */

}

@media (max-width: 768px) {
    .my-zoom {
    background-size: cover; /* Bild deckt den Container ab */
    background-position: center; /* Bild wird zentriert */
    width: 100%; /* Container breitet sich über die gesamte Breite aus */

}
}

@media (max-width: 568px) {
    .my-zoom {
    background-size: cover; /* Bild deckt den Container ab */
    background-position: center; /* Bild wird zentriert */
    width: 100%; /* Container breitet sich über die gesamte Breite aus */
    height: 200px; /* Feste Höhe oder flexibel mit min-height */
}

#pagination {
        width: 100%;
    }
}

    </style>
@endsection
