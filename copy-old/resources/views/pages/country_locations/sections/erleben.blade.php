<section id="erleben" class="section section-no-border bg-color-primary m-0">
    <div class="container">
        <div class="row col-12">
            <div class="col-12">
                <h2 class="text-color-dark font-weight-extra-bold text-end">Was kann man in {{ $location->title }} erleben?</h2>
            </div>
            <div class="col-3">
                    <div style="transform: rotate(345deg);width: max-content;position: relative;max-width: 300px;margin-left: 20px;top:20px">
                        <button class="border-0 p-0" data-bs-toggle="modal" data-bs-target="#erleben_picture1_modal">
                            <img src="{{ asset("img/location_main_img/{$location->continent->alias}/{$location->country->alias}/klimatabelle-{$location->alias}.jpg") }}" class="figure-img img-fluid custom-border my-zoom" alt="" >
                        </button>
                    </div>

                    <div style="position: relative;max-width: 300px;transform: rotate(355deg);width: max-content;margin-left: 20px;top:50px">
                        <button class="border-0 p-0" data-bs-toggle="modal" data-bs-target="#erleben_picture2_modal">
                            <img src="{{ asset("img/location_main_img/{$location->continent->alias}/{$location->country->alias}/klima-{$location->alias}.jpg") }}" class="figure-img img-fluid custom-border my-zoom" alt="" >
                        </button>
                    </div>
            </div>
            <div class="col-9 bg-white p-3 ps-5 row">
                <div class="col-12 ps-5">
                    @foreach(explode('  ', $location->{'text_what-to-do'}) as $parg)
                        <p class="text-black">{{ $parg }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
