<section id="experience" class="section section-secondary section-no-border mt-5 mb-5 pt-0">
    <div class="container">
        <div class="row">
            @foreach($countries as $country)
                <div class="col-3 p-1" style="height: 300px;">
                    @php
                        // Hole die erste Location des Landes und deren primäres Bild
                        $location = $country->locations()->first();
                        $primaryImage = $location?->primaryImage()
                            ?? asset('img/default-location.png');
                    @endphp

                    <a href="{{ route('list-country-locations', ['continentAlias' => $continent->alias, 'countryAlias' => $country->alias]) }}">
                        <div
                            style="background-repeat: no-repeat; background-size :cover; background-position :center;
                                   background-image: url('{{ $primaryImage }}')"
                            class="full-width h-100 figure-img img-fluid custom-border d-flex">
                            <div class="mt-auto ms-auto">
                                <div class="bg-opacity-75 bg-white rounded text-dark p-2 m-2">
                                    {{ $country->title }}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
