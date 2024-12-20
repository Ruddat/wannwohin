<section class="section section-no-border bg-color-light m-0 pb-0" style="background-color: #eaeff5 !important;">
    <div class="container" style="background-color: #eaeff5">
        <div class="row">
            <div class="col-7">
                <div style="background-repeat: no-repeat; background-size :cover;background-position :center;background-image: url('{{ asset("img/location_main_img/{$location->continent->alias}/{$location->country->alias}/beste-reisezeit-{$location->alias}.jpg") }}')"
                     class="full-width h-100 figure-img img-fluid custom-border d-flex">
                    <div class="mt-auto ms-auto">
                        <div class="bg-opacity-75 bg-white rounded text-dark p-2 m-2">{{ $location->text_pic1 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-5">
                <div class="card">
                    <div class="card-header p-4" style="background-color: #dbdbdb!important;">
                        <div class="text-center mb-4"><h4 class="text-uppercase">FAKTENCHECK</h4></div>
                        <div class="row">
                            <div class="col-4 text-end"><h5 class="mb-0">{{ $location->title }}</h5></div>
                            <div class="col-4">
                                <div id="location_flag" style="background-image : url('{{ asset("img/location_main_img/{$location->continent->alias}/{$location->country->alias}/flagge-{$location->country->alias}.gif") }}')" class="circle custom-border rounded-circle align-self-center"></div>
                            </div>
                            <div class="col-4"><h5 class="mb-0 text-start">{{ $location->country->title }}</h5></div>
                        </div>
                    </div>
                    <div class="card-body bg-white pt-5 box-shadow-2">
                        <table id="location_table" class="table table-sm text-center mb-0">
                            <tr>
                                <td>
                                    <span>OPTIMIZE</span>
                                    <div><h5 class="m-0">???????</h5></div>
                                </td>
                                <td>
                                    <span>HAUPTSTADT</span>
                                    <div><h5 class="m-0">{{ $location->country->capital }}</h5></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>zeitverschiebung</span>
                                        <div><h5 class="m-0">{{$location_time}}</h5></div>
                                </td>
                                <td>
                                    <span>{{ $lang_label }}</span>
                                    <div><h5 class="m-0">{{ $location->country->official_language }}</h5></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>PREISTENDENZ</span>
                                    <div><h5 class="m-0">{{ \LocationImgHelper::get_average($location->country->bsp_in_USD) }}</h5></div>
                                </td>
                                <td>
                                    <span>Währung</span>
                                    <div><h5 class="m-0">{{ $location->country->currency_code }}</h5></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>VISUM & REISEPASS</span>
                                    <div><h5 class="m-0">{{ $location->country->country_visum_needed ? 'Nicht nötig' : $location->country->country_visum_max_time }}</h5></div>
                                </td>
                                <td>
                                    <span>STROMNETZ</span>
{{--                                    <div><h5 class="m-0">{{ $location->country->electric->power }}</h5></div>--}}
                                    <div><h5 class="m-0">{{ $location->electric->power }}</h5></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>FLUGZEIT</span>
                                    <div><h5 class="m-0">{{ ceil($location->flight_hours) }} Stunden</h5></div>
                                </td>
                                <td>
                                    <span>ENTFERNUNG</span>
                                    <div><h5 class="m-0">{{  number_format($location->dist_from_FRA, 0, ",", ".")  }} Km</h5></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                {{--Price of location--}}
                <div class="row bg-white py-3 my-2 text-center" style="margin-right: 0.10rem!important; margin-left: 0.10rem!important;">
                    <div class="col-4 border-right">
                        <i class="fa fa-hotel text-5"></i>
                        <span class="d-block">Unterkünfte</span>
                        <div class="d-block">ab {{ number_format($location->price_hotel , 0, ",", ".")  }} €</div>
                    </div>
                    <div class="col-4 border-right">
                        <i class="fa fa-plane text-5"></i>
                        <span class="d-block">Flüge</span>
                        <div class="d-block">ab {{ number_format($location->price_flight, 0, ",", ".") }} €</div>
                    </div>
                    <div class="col-4">
                        <i class="fa fa-car text-5"></i>
                        <span class="d-block">Mietwagen</span>
                        <div class="d-block">ab {{ number_format($location->price_rental , 0, ",", ".") }} €</div>
                    </div>
                </div>

                {{--activity of location--}}
{{--                <div class="row bg-white py-3 my-2 text-center" style="margin-right: 0.10rem!important; margin-left: 0.10rem!important;">--}}
{{--                    <div class="col-4 border-right">--}}
{{--                        <i class="fa fa-hotel text-5"></i>--}}
{{--                        <span class="d-block">Unterkünfte</span>--}}
{{--                        <div class="d-block">ab {{ number_format($location->price_hotel , 0, ",", ".")  }} €</div>--}}
{{--                    </div>--}}
{{--                    <div class="col-4 border-right">--}}
{{--                        <i class="fa fa-plane text-5"></i>--}}
{{--                        <span class="d-block">Flüge</span>--}}
{{--                        <div class="d-block">ab {{ number_format($location->price_flight, 0, ",", ".") }} €</div>--}}
{{--                    </div>--}}
{{--                    <div class="col-4">--}}
{{--                        <i class="fa fa-car text-5"></i>--}}
{{--                        <span class="d-block">Mietwagen</span>--}}
{{--                        <div class="d-block">ab {{ number_format($location->price_rental , 0, ",", ".") }} €</div>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="row d-flex flex-wrap w-100 bg-white py-2 my-2 text-center" style="margin-right: 0.10rem!important; margin-left: 0.10rem!important;">
{{--                    <div class="col-12 d-flex align-items-end justify-content-start">--}}
                            {{--location activities component   --}}
                            {{--https://www.wann-wohin.de/europa/spanien/fuerteventura--}}
                            <x-location-activities
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
{{--                        </div>--}}
{{--                    </div>--}}
                </div>




            </div>
        </div>
{{--        <div class="row bg-white p-2 mx-1 mt-3">--}}
{{--            <div class="col-3">--}}
{{--                <div><i class="fa fa-home"></i>?????????????</div>--}}
{{--                <div><i class="fa fa-home"></i>?????????????</div>--}}
{{--            </div>--}}
{{--            <div class="col-3">--}}
{{--                <div><i class="fa fa-home"></i>?????????????</div>--}}
{{--                <div><i class="fa fa-home"></i>?????????????</div>--}}
{{--            </div>--}}
{{--            <div class="col-3">--}}
{{--                <div><i class="fa fa-home"></i>?????????????</div>--}}
{{--                <div><i class="fa fa-home"></i>?????????????</div>--}}
{{--            </div>--}}
{{--        </div>--}}
    </div>
</section>
