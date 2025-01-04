<section class="section section-no-border bg-color-light m-0 pb-0" style="background-color: #eaeff5 !important;">
    <div class="container">
    <h1 class="text-center mb-4">Locations in {{ $country->title }}</h1>
    <div class="row">
        @foreach($locations as $location)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                <div class="card h-100">
                    <!-- Bild mit fester Größe -->
                    <img src="{{ $location->primaryImage() }}" class="card-img-top" alt="{{ $location->title }}" style="object-fit: cover; height: 200px;">

                    <div class="card-body d-flex flex-column">
                        <!-- Titel -->
                        <h5 class="card-title text-truncate">{{ $location->title }}</h5>

                        <!-- Text mit Begrenzung -->
                        <p class="card-text">
                            {{ Str::limit(strip_tags($location->text_short), 200) }}
                        </p>



                        <!-- "More"-Link -->
                        @if(strlen(strip_tags($location->text_short)) > 100)
                        <a href="{{ route('location.details', [
                            'continent' => $location->country->continent->alias,
                            'country' => $location->country->alias,
                            'location' => $location->alias,
                        ]) }}" class="mt-auto btn btn-sm btn-primary">
                            More
                        </a>
                    @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
</section>
<style>
.card-text {
    overflow: hidden;
    text-overflow: ellipsis; /* Fügt "..." hinzu */
    display: -webkit-box;
    -webkit-line-clamp: 3; /* Anzahl der Zeilen */
    -webkit-box-orient: vertical;
    height: auto; /* Automatische Höhe basierend auf Inhalt */
}

</style>
