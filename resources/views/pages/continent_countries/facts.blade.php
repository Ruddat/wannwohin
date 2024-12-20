<section class="section section-no-border bg-color-light m-0 pb-0" style="background-color: #eaeff5 !important;">
    <div class="container" style="background-color: #eaeff5">
        <div class="row">
{{--            <div class="col-7 flex-0-0-auto">--}}
{{--                <div class="card border-0 box-shadow-4">--}}
{{--                    <div class="card-img-top position-relative overlay">--}}

{{--                    </div>--}}
{{--                    <div class="card-body p-4">--}}
{{--                        <span class="d-block text-color-grey font-weight-semibold positive-ls-2 text-2">--}}
{{--                        <span class="d-block">--}}
{{--                                            {{ $country->country_text}}--}}
{{--                        </span>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

            <div class="col-7">
{{--                <div style="background-repeat: no-repeat; background-size :cover;background-position :center;" class="full-width h-100 figure-img img-fluid custom-border d-flex">--}}
                <div class="full-width h-100 custom-border d-flex box-shadow-4 bg-color-white">
                    <div class="p-2 m-2">
{{--                        <div class="bg-opacity-75 bg-white rounded text-dark p-2 m-2"> {{ $country->country_text}}--}}{{--</div>--}}
                       {{ $continent->continent_text}}
                    </div>
                </div>
            </div>

            <div class="col-5">
                <div class="card">
                    <div class="card-header p-4" style="background-color: #dbdbdb!important;">
                        <div class="text-center mb-4"><h4 class="text-uppercase">{{ $continent->title }}</h4></div>
                        <div class="row">
                            <div class="col-4 text-end"><h5 class="mb-0"></h5></div>
                            <div class="col-4">
                                <div id="location_flag" style="background-image : url('{{ asset("img/location_main_img/{$continent_alias}.png") }}')" class="circle custom-border rounded-circle align-self-center"></div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="card-body bg-white pt-5 box-shadow-2">
                        <table id="location_table" class="table table-sm text-center mb-0">
                            <tr>
                                <td>
                                    <span>area_km</span>
                                    <div><h5 class="m-0">{{ $continent->area_km }}</h5></div>
                                </td>
                                <td>
                                    <span>population</span>
                                    <div><h5 class="m-0">{{ $continent->population }}</h5></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
