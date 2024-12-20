<section id="experience" class="section section-secondary section-no-border mt-5 mb-5 pt-0">
    <div class="container">
        <div class="row">
            @foreach($countries as $country)
                <div class="col-3 p-1" style="height: 300px;">
                    {{--<div style="background-repeat: no-repeat; background-size :cover;background-position :center;background-image: url('{{ asset("img/location_main_img/{$continent_alias}/{$country->alias}/beste-reisezeit-{$location->alias}.jpg") }}')" class="full-width h-100 figure-img img-fluid custom-border d-flex">--}}
                    <a href="{{  route('list-country-locations', ['continent' => $continent_alias, 'country' => $country->alias])}}">
                        <div
{{--                            style="background-repeat: no-repeat; background-size :cover;background-position :center;background-image: url('{{ asset("img/location_main_img/{$continent_alias}/{$country->alias}/flagge-{$country->alias}.png") }}')"--}}
                            style="background-repeat: no-repeat; background-size :cover;background-position :center;background-image: url('{{ asset("img/location_main_img/{$continent_alias}/urlaub-in-{$country->alias}.jpg") }}')"
                            class="full-width h-100 figure-img img-fluid custom-border d-flex">
                            <div class="mt-auto ms-auto">
                                <div class="bg-opacity-75 bg-white rounded text-dark p-2 m-2">{{$country->title}}</div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

