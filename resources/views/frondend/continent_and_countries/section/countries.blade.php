<section id="experience" class="experience-section section section-secondary mt-5 mb-5 pt-0">
    <div class="container">

        <!-- Banner oberhalb -->
        <div class="row mb-4">
            <div class="col-12">
                <x-ad-block position="above-experience" />
            </div>
        </div>

        @php
            $countriesArray = $countries->toArray();
            $totalCountries = count($countriesArray);

            $ads = \App\Models\ModAdvertisementBlocks::where('is_active', 1)
                ->where('position', 'inline')
                ->inRandomOrder()
                ->limit(3)
                ->get();

            $availableAds = $ads->count();
            $adCount = min(max(1, floor($totalCountries / 3)), $availableAds);

            $adPositions = $totalCountries > 0 && $adCount > 0
                ? array_rand(array_keys($countriesArray), $adCount)
                : [];
            $adPositions = is_array($adPositions) ? $adPositions : [$adPositions];

            $adAssignments = [];
            foreach ($adPositions as $key => $position) {
                $adAssignments[$position] = $ads[$key] ?? null;
            }
        @endphp

        <div class="row g-4">
            @foreach($countries as $index => $country)
                <div class="col-12 col-sm-6 col-md-6 col-lg-4 experience-item"
                     data-aos="fade-up"
                     data-aos-duration="400"
                     data-aos-delay="{{ $index * 50 }}">
                    @if(array_key_exists($index, $adAssignments))
                        <!-- Werbe-Kachel -->
                        <div class="experience-card card border-0 shadow-lg h-100 ad-card">
                            <div class="card-body p-0">
                                <div class="ad-content">
                                    {!! $adAssignments[$index]->script !!}
                                </div>
                                <div class="ad-footer">
                                    <small class="text-muted">Werbung | Ad ID: {{ $adAssignments[$index]->id }}</small>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Länder-Kachel -->
                        @php
                            $primaryImage = $country->primaryImage() ?? asset('img/default-location.png');
                        @endphp
                        <a href="{{ route('list-country-locations', ['continentAlias' => $continent->alias, 'countryAlias' => $country->alias]) }}"
                           class="text-decoration-none">
                            <div class="experience-card card border-0 shadow-lg">
                                <div class="card-img-wrapper" style="background-image: url('{{ $primaryImage }}');">
                                    <div class="card-overlay"></div>
                                </div>
                                <div class="card-body d-flex align-items-end">
                                    <div class="card-title-wrapper bg-opacity-75 bg-white rounded text-dark p-3">
                                        <h4 class="m-0 text-center">{{ $country->title }}</h4>
                                    </div>
                                </div>
                            </div>
                        </a>

                        @auth('admin')
                            <div class="mt-2 text-center">
                                <a href="{{ route('country-manager.edit', $country->id) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-warning">
                                    <i class="ti ti-edit"></i> Bearbeiten
                                </a>
                            </div>
                        @endauth
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Banner unterhalb -->
        <div class="row mt-4">
            <div class="col-12">
                <x-ad-block position="below-experience" />
            </div>
        </div>

    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    AOS.init({
        duration: 400,  // Schnellere Animation
        delay: 0,      // Basis-Delay (individuell via data-aos-delay)
        once: true     // Nur einmal animieren
    });
});
</script>

<style scoped>
.experience-section .experience-card {
    height: 350px;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.experience-section .card-img-wrapper {
    height: 100%;
    background-size: cover;
    background-position: center;
    position: relative;
    transition: transform 0.5s ease;
}

.experience-section .card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.2);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.experience-section .experience-card:hover .card-img-wrapper {
    transform: scale(1.1); /* Zoom-Effekt */
}

.experience-section .experience-card:hover .card-overlay {
    opacity: 1; /* Farbüberlagerung bei Hover */
}

.experience-section .experience-card:hover {
    transform: translateY(-5px); /* Leichtes Anheben */
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.25); /* Stärkerer Schatten */
}

.experience-section .card-body {
    padding: 0;
    position: absolute;
    bottom: 0;
    width: 100%;
}

.experience-section .card-title-wrapper {
    width: fit-content;
    max-width: 100%;
    font-size: 1.2rem;
    transition: background-color 0.3s ease;
}

.experience-section .experience-card:hover .card-title-wrapper {
    background-color: rgba(255, 255, 255, 0.9); /* Leichte Farbänderung */
}

.experience-section .card-title-wrapper h4 {
    font-size: 1.5rem;
    font-weight: bold;
}

@media (max-width: 992px) {
    .experience-section .experience-card {
        height: 300px;
    }
}

@media (max-width: 768px) {
    .experience-section .experience-card {
        height: 250px;
    }
    .experience-section .card-title-wrapper h4 {
        font-size: 1.25rem;
    }
}

/* Werbe-Kacheln spezifisch */
.ad-card {
    display: flex;
    flex-direction: column;
    background-color: #f8f9fa; /* Leichter grauer Hintergrund */
    padding: 15px;
    box-sizing: border-box;
}

.ad-card .card-body {
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* Inhalt oben, Footer unten */
    height: 100%;
    width: 100%;
    position: relative;
}

.ad-card .ad-content {
    flex-grow: 1; /* Nimmt den verfügbaren Raum ein */
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    overflow: hidden;
}

.ad-card .ad-footer {
    text-align: center;
    padding-top: 10px;
    font-size: 0.85rem;
    color: #6c757d; /* Muted Text */
    background: rgba(255, 255, 255, 0.8); /* Leicht transparenter Hintergrund */
    width: 100%;
}

.ad-card .ad-footer small {
    display: block;
}
</style>
