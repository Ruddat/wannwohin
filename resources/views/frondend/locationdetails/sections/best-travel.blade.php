<div class="container my-4">
    <article class="timeline-box right custom-box-shadow-2 box-shadow-2">
    <div class="row">
        <!-- Image Section -->
        <div class="experience-info col-lg-3 col-sm-5 bg-color-primary p-0 article-img d-flex"
             style="background-image: url('https://www.wann-wohin.de/assets/img/location/lage-klima.jpg'); background-size: cover; background-position: center;">
        </div>

        <!-- Description Section -->
        <div class="experience-description col-lg-9 col-sm-7 bg-color-light px-4 py-3 rounded-end">
            <h4 class="text-color-dark font-weight-semibold">Beste Reisezeit {{ $location->title }}</h4>

            <div class="formatted-text">
                {!! app('autotranslate')->trans($location->text_best_traveltime, app()->getLocale()) !!}
            </div>

            <p class="text-black">
                Die beste Reisezeit {{ $location->title }} kennenzulernen sind die folgenden Monate:
            </p>

            <!-- Monats-Kalender Karten -->
            <div class="d-flex flex-wrap gap-2">
                @foreach($best_travel_months as $index => $month)
                    <div class="text-center">
                        <img src="{{ asset('img/best_travel_time/' . $index . '.png') }}"
                             alt="Reisezeit Monat {{ $month }}"
                             class="img-fluid"
                             style="max-width: 70px;">
                        <p>{{ $month }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</article>
</div>
