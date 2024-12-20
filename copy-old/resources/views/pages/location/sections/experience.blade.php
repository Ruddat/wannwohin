<section id="experience" class="section section-secondary section-no-border m-0 pt-0">
    <div class="container">
        <div class="row">
            <div class="row col-12">
                <div class="col">
                    <section class="timeline custom-timeline" id="timeline">
                        <div class="timeline-body">
                            <x-img-text>
                                <x-slot name="imgContent" style="background-image: url('{{ LocationImgHelper::getKlimatabelleImg($location) }}')">
                                </x-slot>
                                    <h4 class="text-color-dark font-weight-semibold">{{ $location->text_headline }}</h4>{{----}}
                                    @foreach(explode('  ', $location->text_short) as $parg)
                                        <p class="text-black">{{ $parg }}</p>
                                    @endforeach
                                <div class="d-flex">
                                    <button class="ms-auto btn btn-primary" data-bs-toggle="modal" data-bs-target="#google_map_modal">
                                        Position auf der Karte
                                    </button>
                                </div>
                            </x-img-text>

                            <x-img-text>
                                <x-slot name="imgContent" class="d-flex" style="background-image: url('{{ asset('assets/img/location/lage-klima.jpg') }}')">
                                </x-slot>
                                <h4 class="text-color-dark font-weight-semibold">Beste Reisezeit {{ $location->title }}</h4>
                                @foreach(explode('  ', $location->{'text_best-traveltime'}) as $parg)
                                    <p class="text-black">{{ $parg }}</p>
                                @endforeach
                                <div>
                                    @foreach(explode(',', $location->best_traveltime) as $month)
                                        <img src="{{ asset('img/best_travel_time/'.$month.'.png') }}" alt="">
                                    @endforeach
                                </div>
                            </x-img-text>

                            <x-img-text class="box-shadow-2">
{{--                                <x-slot name="imgContent" style="background-image: url('{{ asset("img/location_main_img/{$location->continent->alias}/{$location->country->alias}/karte-{$location->alias}.webp") }}')">--}}
{{--                                </x-slot>--}}
                                <x-slot name="imgContent" class="d-flex" style="background-image: url('{{ asset('img/location_main_img/'.$location->continent->alias.'/'.$location->country->alias.'/karte-'.$location->country->alias.'.webp') }}')">
{{--                                    <div class="">--}}
{{--                                        <div class="my-auto">sdv</div>--}}
{{--                                        <div class="my-auto">sdv</div>--}}
{{--                                        <div class="my-auto">sdv</div>--}}
{{--                                    </div>--}}
                                </x-slot>
                                <h4 class="text-color-dark font-weight-semibold">Lage und Klima</h4>
                                @foreach(explode('  ', $location->{'text_location-climate'}) as $parg)
                                    <p class="text-black">{{ $parg }}</p>
                                @endforeach
{{--                                <div class="d-flex">--}}
{{--                                    <a class="ms-auto btn btn-primary" target="_blank" href="https://www.klimatabelle.de/klima/{{ $location->continent->alias }}/{{ $location->country->alias }}/klimatabelle-{{ $location->alias }}.htm">Mehr zu Klima & Wetter</a>--}}
{{--                                </div>--}}
                            </x-img-text>

                            {{-- klima und wetter box--}}
                            <article class="timeline-box right custom-box-shadow-2 box-shadow-2">
                                <div class="row">
                                    <div class="experience-info col-lg-3 col-sm-5 bg-info p-0 ">
                                        @include('pages.location.sections.weather')
                                    </div>
                                    <div class="experience-description col-lg-9 col-sm-7 bg-color-light px-2 ">
                                        @include('pages.location.sections.climate_table')
                                    </div>
                                </div>
                            </article>
                            <div class="timeline-bar"></div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="col-2"></div>
        </div>
    </div>
</section>


