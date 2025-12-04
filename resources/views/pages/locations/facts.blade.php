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
                       {{ $country->country_text}}
                       
                    </div>
                </div>
            </div>

            <div class="col-5">
                <div class="card">
                    <div class="card-header p-4" style="background-color: #dbdbdb!important;">
                        <div class="text-center mb-4"><h4 class="text-uppercase">{{ $country->title }}</h4></div>
                        <div class="row">
                            <div class="col-4 text-end"><h5 class="mb-0"></h5></div>
                            <div class="col-4">
                                <div id="location_flag" style="background-image : url('{{ asset("img/location_main_img/{$continent_alias}/{$country->alias}/flagge-{$country->alias}.gif") }}')" class="circle custom-border rounded-circle align-self-center"></div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="card-body bg-white pt-5 box-shadow-2">
                        <table id="location_table" class="table table-sm text-center mb-0">
                            <tr>
                                <td>
                                    <span>Währung</span>
                                    <div><h5 class="m-0">{{ $country->currency_code }}</h5></div>
                                </td>
                                <td>
                                    <span>HAUPTSTADT</span>
                                    <div><h5 class="m-0">{{ $country->capital }}</h5></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>PREISTENDENZ</span>
                                    <div><h5 class="m-0">{{ \LocationImgHelper::get_average($country->bsp_in_USD) }}</h5></div>
                                </td>
                                <td>
                                    <span>{{ count(explode(',', $country->official_language)) > 1 ? 'Sprachen' : 'Sprache' }}</span>
                                    <div><h5 class="m-0">{{ $country->official_language }}</h5></div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <span>VISUM & REISEPASS</span>
                                    <div><h5 class="m-0">{{ $country->country_visum_needed ? 'Nicht nötig' : $country->country_visum_max_time }}</h5></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
