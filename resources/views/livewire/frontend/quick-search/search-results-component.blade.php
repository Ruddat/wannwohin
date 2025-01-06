<div role="main" class="main">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- Sortierauswahl -->
        <div class="sort-controls">
            <label for="sort_by">Sortieren:</label>
            <select id="sort_by" class="form-select" wire:model.change="sortBy">
                <option value="title">Reiseziel</option>
                <option value="price_flight">Preis</option>
                <option value="sunshine_per_day">Sonnenstunden</option>
                <option value="flight_hours">Flugdauer</option>
            </select>
        </div>
    </div>

    <section class="timeline custom-timeline" id="timeline">
        <div class="timeline-body">
            @forelse($locations as $location)
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
            @empty
                <p class="text-center">Keine Ergebnisse gefunden.</p>
            @endforelse
        </div>
    </section>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $locations->links() }}
    </div>
</div>
</div>
