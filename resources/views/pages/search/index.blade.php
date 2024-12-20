@extends('layouts.main')

@section('content')
    <div role="main" class="main">
        <section id="experience" class="section section-secondary section-no-border m-0 pb-0 bg-white">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <section class="timeline custom-timeline" id="timeline">
                            <div class="timeline-body">
                                @foreach($locations as $location)
                                    <x-img-text contentClass="p-4">
                                        <x-slot name="imgContent" style="background-image: url('{{ LocationImgHelper::getKlimatabelleLocalImg($location) }}')"></x-slot>
                                        <div class="row">
                                            <div class="col-4">
                                                <a href="{{ route('location', [$location->continent->alias, $location->country->alias, $location->alias]) }}"><h4 class="text-10 text-dark mb-4">{{ $location->title }}</h4></a>
                                            </div>
                                            <div class="col-3 mb-3">
                                                <div class="d-flex justify-content-center align-items-start">
                                                    <img src="{{ asset('assets/img/location/sun.png') }}" alt="" class="me-3" width="64" height="64"/>
                                                    <img src="{{ asset('assets/img/location/sunny-weather-clip-and-sun.png') }}" alt="" class="me-3" width="64" height="64"/>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <h5 class="text-8 text-dark mb-4 text-end">ab {{ $location->Price_Flight . ' €' }}</h5>
                                            </div>

                                            <div class="col-4">
                                                <div class="d-inline">
                                                    <img src="{{ LocationImgHelper::getCountryImg($location) }}" alt="" class="me-3"/>{{ $location->country->title }}
                                                </div>
                                                <hr class="bg-color-dark my-2">
                                                <div class="d-inline">
                                                    <img src="{{ LocationImgHelper::getContinentImg($location) }}" alt="" class="me-3"/>{{ $location->continent->title }}
                                                </div>
                                                <hr class="bg-color-dark my-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-arrows-alt-h text-6 me-4"></i>{{ ceil($location->flight_hours) }} Flugstunden
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="d-flex justify-content-around">
                                                    <div>Tagsüber</div><div>{{ $location->climateMonth($month)->daily_temperature }}℃</div>
                                                </div>
                                                <hr class="bg-color-dark my-2">
                                                <div class="d-flex justify-content-around">
                                                    <div>Wasser</div><div>{{ $location->climateMonth($month)->water_temperature }}℃</div>
                                                </div>
                                                <hr class="bg-color-dark my-2">
                                                <div class="d-flex justify-content-around">
                                                    <div>Sonne </div><div>{{ $location->climateMonth($month)->sunshine_per_day }} h</div>
                                                </div>
                                            </div>

                                            <div class="col-5">
                                                <div>&nbsp;</div>
                                                <hr class="bg-color-dark my-2">
                                                <div class="row">
                                                    <div class="col-6 ps-4">Regentage</div><div class="col-6">{{ $location->climateMonth($month)->rainy_days }}</div>
                                                </div>
                                                <hr class="bg-color-dark my-2">
                                                <div class="row">
                                                    <div class="col-6 ps-4">Beste Reisezeit</div><div class="col-6">{{ \ThemeTextHelper::monthArray2String($location->best_traveltime_json) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </x-img-text>
                                @endforeach
                                <div class="timeline-bar"></div>
                            </div>
                        </section>

                    </div>
                </div>
            </div>
        </section>

        <div class="container py-2">
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
        </script>

    </div>
@endsection
