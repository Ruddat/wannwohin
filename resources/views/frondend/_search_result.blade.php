@extends('layouts.main')

@section('content')
    <div role="main" class="main">
        <section id="experience" class="section section-secondary section-no-border m-0 pb-0 bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="float-end d-inline-flex align-items-center">
                            <span id="sort_result_direction" data-sort-direction="{{ (isset(request()->sort_direction) && request()->sort_direction=="desc")? "desc" : "asc" }}" class="sort-direction pe-2 d-inline-flex flex-column cursor-click">
                                {{--  Sort Up (Ascending) Icon  Ascending smallest to largest, 0 to 9 --}}
                                <i id="sort_result_up" class="fas fa-sort-up fa-lg {{ (isset(request()->sort_direction) && request()->sort_direction=="desc")? "fa-disabled" : "" }}" style="line-height: 3px"></i>
                                <i id="sort_result_down" class="fas fa-sort-down fa-lg {{ (!isset(request()->sort_direction) || request()->sort_direction=="asc")? "fa-disabled" : "" }}" style="line-height: 3px"></i>
                            </span>
                            <label for="" class="pe-1 text-4">Sortieren: </label>
                            <select class="form-select" id="search_result_sort" name="search_result_sort">
                                @foreach($sort_by_criteria as $key => $value)
                                    <option value="{{ $key }}" {{ (request()->sort_by == $key) || (request()->sort_by == '' && $key== 'location') ? " selected" : "" }}>{{ $value['title'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <section class="timeline custom-timeline" id="timeline">
                            <div class="timeline-body">
                                @foreach($locations as $location)
                                    <article class="timeline-box right custom-box-shadow-2">
                                        <div class="row">
                                            <div class="experience-info col-lg-3 col-sm-5 bg-color-primary p-0 m-0 overflow-hidden">
                                                <a href="{{ route('location', [$location->continent_alias, $location->country_alias, $location->alias]) }}" class="p-0 m-0">
                                                    <div class="my-zoom" style="background-image: url('{{ "/img/location_main_img/$location->continent_alias/$location->country_alias/{$location->alias}/klimatabelle-$location->alias.jpg" }}')">
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="experience-description col-lg-9 col-sm-7 bg-color-light">
                                                {{--                                                title row--}}
                                                <div class="row">
                                                    <div class="col-4">
                                                        <a href="{{ route('location', [$location->continent_alias, $location->country_alias, $location->alias]) }}"><h4 class="text-7 text-dark mb-4">{{ $location->title }}</h4></a>
                                                    </div>
                                                    <div class="col-3 mb-3">
                                                        <div class="d-flex justify-content-start  align-items-start">
                                                            {{--                                                    <img src="{{ asset('assets/img/location/sun.png') }}" alt="" class="me-3" width="64" height="64"/>--}}
                                                            {{--                                                            <h5 class="text-5 text-dark d-block mb-4 me-2"> {{ $location->climates_month }}</h5> <img src="{{ asset('assets/img/location/sunny-weather-clip-and-sun.png') }}" alt="" class="me-3" width="48" height="48"/>--}}
                                                            <h5 class="text-5 text-dark d-block mb-4 me-2"> im {{ $location->climates_month }}</h5>
                                                        </div>
                                                    </div>
                                                    <div class="col-5">
                                                        {{--                                                        <h5 class="text-7 text-dark mb-4 text-end">ab {{ $location->price_flight . ' €' }}</h5>--}}
                                                        <h5 class="text-7 text-dark mb-4 text-end">ab {{ number_format( $location->price_flight, 0, ',', '.') . ' €' }}</h5>
                                                    </div>
                                                </div>
                                                {{--                                                \title row--}}
                                                {{--                                                line 1--}}
                                                <div class="row my-3 pb-1">
                                                    <div class="col-4 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            <img src="img/flags_small/{{$location->country_alias}}.jpg" alt="" class="me-4"/>{{ $location->country_title }}
                                                        </div>
                                                    </div>
                                                    <div class="col-3 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            <div class="col-6">Tagsüber</div><div class="col-4">{{ $location->climates_daily_temperature }}℃</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-5 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            <x-location-search-location-activities
                                                                listBeach="{{$location->list_beach}}"
                                                                listCitytravel="{{$location->list_citytravel}}"
                                                                dailyTemperature="{{$location->climates_daily_temperature}}"
                                                                listSports="{{$location->list_sports}}"
                                                                listIsland="{{$location->list_island}}"
                                                                listCulture="{{$location->list_culture}}"
                                                                listNature="{{$location->list_nature}}"
                                                                listWatersport="{{$location->list_watersport}}"
                                                                listWintersport="{{$location->list_wintersport}}"
                                                                listMountainsport="{{$location->list_mountainsport}}"
                                                            />
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="row my-3">
                                                    <div class="col-4 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            <img src="{{asset("img/location_main_img/".$location->continent_alias.".png")}}" alt="" class="me-3"/>{{ $location->continent_title }}
                                                        </div>
                                                    </div>
                                                    <div class="col-3 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            @if($location->climates_water_temperature !='')
                                                                <div class="col-6">Wasser</div><div class="col-4">{{ $location->climates_water_temperature }}℃</div>
                                                            @else
                                                                <div class="col-8">Luftfeuchtigkeit</div><div class="col-4">{{ $location->climates_humidity }} %</div>
                                                            @endif
                                                        </div>

                                                    </div>
                                                    <div class="col-5 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            <div class="col-5">Regentage</div><div class="col-7">{{ $location->climates_rainy_days }}</div>
                                                        </div>
{{--                                                        <hr class="bg-color-dark">--}}
                                                    </div>
                                                </div>
                                                <div class="row my-3">
                                                    <div class="col-4 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            <i class="fas fa-arrows-alt-h text-6 me-3"></i>{{ ceil($location->flight_hours) }} Flugstunden
                                                        </div>
{{--                                                        <hr class="bg-color-dark me-1">--}}
                                                    </div>
                                                    <div class="col-3 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            <div class="col-6">Sonne   </div>
                                                            <div class="col-4"> {{ $location->climates_sunshine_per_day }} h</div>
                                                        </div>
{{--                                                        <hr class="bg-color-dark me-1">--}}
                                                    </div>
                                                    <div class="col-5 d-flex align-items-end justify-content-start">
                                                        <div class="d-flex pb-2 border-bottom w-100">
                                                            <div class="col-5">Beste Reisezeit</div><div class="col-7">{{ \ThemeTextHelper::monthArray2String(json_decode($location->best_traveltime_json, true)) }}</div>
                                                        </div>
{{--                                                        <hr class="bg-color-dark">--}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                                <div class="timeline-bar"></div>
                            </div>
                        </section>

                    </div>
                </div>
            </div>
        </section>

        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    {{--            {{ $locations->withQueryString()->links() }}--}}
                    {{ $locations->appends(compact('items_per_page'))->withQueryString()->links() }}
                </div>
                <div class="font-weight-bold col-md-2 pt-2 px-0">
                    <div style="float: right" >
                        Ergebnisse pro Seite:
                    </div>
                </div>
                <div class="col-md-1">
                    <select name="pagination" id="pagination" class="form-select d-inline">
                        <option value="10" @if($items_per_page == 10) selected @endif >10</option>
                        <option value="25" @if($items_per_page == 25) selected @endif >25</option>
                        <option value="50" @if($items_per_page == 50) selected @endif >50</option>
                    </select>
                </div>
            </div>
        </div>
        <script>
            document.getElementById('pagination').onchange = function() {
                window.location = "{!! $locations->url(1) !!}&items_per_page=" + this.value;
            };
            document.getElementById('search_result_sort').onchange = function() {
                window.location = "{!! $locations->url(1) !!}&sort_by=" + this.value;
            };
            document.getElementById('sort_result_direction').onclick = function() {
                let direction = this.getAttribute("data-sort-direction") == "desc" ? "asc" : "desc";
                window.location = "{!! $locations->url(1) !!}&sort_direction=" + direction;
            };
        </script>

    </div>
@endsection
