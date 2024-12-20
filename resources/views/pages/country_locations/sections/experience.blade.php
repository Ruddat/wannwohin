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

                            <x-img-text>
                                <x-slot name="imgContent" class="">
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
                                <div class="d-flex">
                                    <a class="ms-auto btn btn-primary" target="_blank" href="https://www.klimatabelle.de/klima/{{ $location->continent->alias }}/{{ $location->country->alias }}/klimatabelle-{{ $location->alias }}.htm">Mehr zu Klima & Wetter</a>
                                </div>
                            </x-img-text>

{{--                            <x-img-text>--}}
{{--                                <x-slot name="imgContent" class="d-flex" style="background-image : url('{{ LocationImgHelper::getKlimaImg($location) }}');">--}}
{{--                                    <div class="mt-auto bg-color-dark w-100 p-2 px-3 pb-4 text-color-white text-center">--}}
{{--                                        <p class="text-color-white" style="text-transform: unset">{{ $location->text_pic2 }}</p>--}}
{{--                                    </div>--}}
{{--                                </x-slot>--}}
{{--                                <h4 class="text-color-dark font-weight-semibold">Was Kann man heir erleben?</h4>--}}
{{--                                @foreach(explode('  ', $location->{'text_what-to-do'}) as $parg)--}}
{{--                                    <p class="text-black">{{ $parg }}</p>--}}
{{--                                @endforeach--}}
{{--                            </x-img-text>--}}

                            <div class="timeline-bar"></div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="col-2"></div>
        </div>
    </div>
</section>


