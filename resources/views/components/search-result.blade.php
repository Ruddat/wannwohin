<article class="timeline-box right custom-box-shadow-2">
    <div class="row">
        <!-- Image Section -->
        <div class="experience-info col-lg-3 col-sm-5 bg-color-primary p-0 m-0 overflow-hidden">
            <a href="{{ route('location.details', [
                'continent' => $location->country->continent->alias,
                'country' => $location->country->alias,
                'location' => $location->alias,
            ]) }}">
                <div class="my-zoom" style="background-image: url('{{ asset("{$location->text_pic1}") }}')"></div>
            </a>
        </div>

        <!-- Content Section -->
        <div class="experience-description col-lg-9 col-sm-7 bg-color-light">
            <h4 class="text-7 text-dark mb-2">{{ $location->title }}</h4>
            <p class="text-5 text-dark mb-2">ab {{ number_format($location->price_flight, 0, ',', '.') }} €</p>
            <!-- Add more details if needed -->
        </div>
    </div>
</article>
