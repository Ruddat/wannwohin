<section id="experience" class="experience-section py-5">
    <div class="container">

        <!-- Banner oberhalb -->
        <div class="row mb-4">
            <div class="col-12">
                <x-ad-block position="above-experience" class="full-width" />
            </div>
        </div>

        @php
            $countriesArray = $countries->toArray();
            $totalCountries = count($countriesArray);

            // Alle Inline-Anzeigen
            $ads = \App\Models\ModAdvertisementBlocks::where('is_active', 1)
                ->where(function ($query) {
                    $query->whereJsonContains('position', 'inline')
                          ->orWhere('position', 'inline');
                })
                ->inRandomOrder()
                ->limit(2)
                ->get();

            // Kiwi-Widget (rechts)
            $kiwiAd = \App\Models\ModAdvertisementBlocks::where('is_active', 1)
                ->where(function ($query) {
                    $query->whereJsonContains('position', 'kiwi-widget')
                          ->orWhere('position', 'kiwi-widget');
                })
                ->inRandomOrder()
                ->first();

            $availableAds = $ads->count();
            $adCount = min(max(1, floor($totalCountries / 3)), $availableAds);

            // Positionen für Werbe-Kacheln (nach jeweils 2-3 Länder-Kacheln)
            $adPositions = [];
            if ($totalCountries > 0 && $adCount > 0) {
                $step = max(2, floor($totalCountries / ($adCount + 1))); // Schrittgröße für Werbe-Positionen
                for ($i = 0; $i < $adCount; $i++) {
                    $position = min($step * ($i + 1), $totalCountries - 1);
                    $adPositions[] = $position;
                }
            }

            $adAssignments = [];
            foreach ($adPositions as $key => $position) {
                $adAssignments[$position] = $ads[$key] ?? null;
            }
        @endphp

        <div class="row">
            <!-- Kacheln -->
            <div class="{{ $kiwiAd ? 'col-12 col-md-8 col-lg-9' : 'col-12' }}" id="kacheln-container-wrapper">
                <div class="row g-4" id="kacheln-container">
                    @php
                        $currentCountryIndex = 0;
                    @endphp
                    @for($i = 0; $i < $totalCountries + $adCount; $i++)
                        @if(array_key_exists($currentCountryIndex, $adAssignments))
                            <!-- Werbe-Kachel (doppelt breit) -->
                            <div class="col-12 col-sm-12 col-md-12 col-lg-8 experience-item mx-auto"
                                 data-aos="fade-up"
                                 data-aos-duration="400"
                                 data-aos-delay="{{ $i * 50 }}">
                                <div class="experience-card card border-0 shadow-lg h-100 ad-card">
                                    <div class="card-body p-0">
                                        <div class="ad-content">
                                            {!! $adAssignments[$currentCountryIndex]->script !!}
                                        </div>
                                        <div class="ad-footer">
                                            <small class="text-muted">Werbung | Ad ID: {{ $adAssignments[$currentCountryIndex]->id }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @php
                                $currentCountryIndex++;
                            @endphp
                        @elseif($currentCountryIndex < $totalCountries)
                            <!-- Länder-Kachel -->
                            @php
                                $country = $countries[$currentCountryIndex];
                                $primaryImage = $country->primaryImage() ?? asset('img/default-location.png');
                            @endphp
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 experience-item"
                                 data-aos="fade-up"
                                 data-aos-duration="400"
                                 data-aos-delay="{{ $i * 50 }}">
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
                            </div>
                            @php
                                $currentCountryIndex++;
                            @endphp
                        @endif
                    @endfor
                </div>
            </div>

            <!-- Kiwi-Widget (rechts) -->
            @if($kiwiAd)
                <div class="col-md-4 col-lg-3 kiwi-widget">
                    <div class="sticky-top" id="kiwi-widget-container">
                        {!! $kiwiAd->script !!}
                        <div class="ad-footer text-center mt-2">
                            <small class="text-muted">Werbung | Ad ID: {{ $kiwiAd->id }}</small>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Banner unterhalb -->
        <div class="row mt-4">
            <div class="col-12">
                <x-ad-block position="below-experience" class="full-width" />
            </div>
        </div>

    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    AOS.init({
        duration: 400,
        delay: 0,
        once: true
    });

    // Höhe des Kiwi-Widgets an Kacheln-Container anpassen
    const kachelnContainer = document.getElementById('kacheln-container');
    const kiwiContainer = document.getElementById('kiwi-widget-container');
    if (kachelnContainer && kiwiContainer) {
        const adjustHeight = () => {
            const kachelnHeight = kachelnContainer.offsetHeight;
            kiwiContainer.style.maxHeight = `${kachelnHeight}px`;
            kiwiContainer.style.overflowY = 'auto';
        };
        adjustHeight();
        window.addEventListener('resize', adjustHeight);
    }
});
</script>

<style scoped>
.experience-section .experience-card {
    height: 350px;
    border-radius: 12px;
    overflow: visible; /* Erlaubt es dem Inhalt, über die Karte hinauszugehen */
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
    transform: scale(1.1);
}

.experience-section .experience-card:hover .card-overlay {
    opacity: 1;
}

.experience-section .experience-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.25);
}

.experience-section .card-body {
    padding: 0;
    position: absolute;
    bottom: 0;
    width: 100%;
    overflow: visible; /* Verhindert Abschneiden innerhalb der card-body */
}

.experience-section .card-title-wrapper {
    width: fit-content;
    max-width: 100%;
    font-size: 1.2rem;
    transition: background-color 0.3s ease;
    white-space: normal; /* Erlaubt Zeilenumbrüche */
    overflow: visible; /* Verhindert Abschneiden */
}

.experience-section .experience-card:hover .card-title-wrapper {
    background-color: rgba(255, 255, 255, 0.9);
}

.experience-section .card-title-wrapper h4 {
    font-size: 1.5rem;
    font-weight: bold;
    white-space: normal; /* Erlaubt Zeilenumbrüche im h4-Element */
    overflow: visible; /* Verhindert Abschneiden */
    line-height: 1.2; /* Reduziert die Zeilenhöhe für bessere Darstellung */
}

/* Werbe-Kacheln spezifisch */
.ad-card {
    display: flex;
    flex-direction: column;
    background-color: #f8f9fa;
    padding: 15px;
    box-sizing: border-box;
}

.ad-card .card-body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    width: 100%;
    position: relative;
}

.ad-card .ad-content {
    flex-grow: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    overflow: hidden;
}

.ad-card .ad-content img {
    width: 100%; /* Volle Breite des Containers */
    height: auto; /* Automatische Höhe basierend auf Breite */
    max-height: 100%; /* Begrenzt die Höhe auf den Container */
    object-fit: contain; /* Proportionale Skalierung ohne Beschneidung */
}

.ad-card .ad-footer {
    text-align: center;
    padding-top: 10px;
    font-size: 0.85rem;
    color: #6c757d;
    background: rgba(255, 255, 255, 0.8);
    width: 100%;
}

.ad-card .ad-footer small {
    display: block;
}

/* Kiwi-Widget spezifisch */
.kiwi-widget {
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 12px;
}

.kiwi-widget .sticky-top {
    top: 15px;
}

#widget-holder {
    width: 100%;
    height: 100%;
}

/* Für above-experience und below-experience */
.ad-card.full-width {
    height: auto; /* Automatische Höhe basierend auf Inhalt */
    max-width: 100%;
}

/* Einheitliche Rundungen für alle Karten */
.experience-section .experience-card,
.experience-section .experience-card .card-img-wrapper,
.experience-section .experience-card .card-overlay,

.ad-card,
.kiwi-widget,
.experience-section .ad-card .card-body,
.experience-section .ad-card .ad-content,
.experience-section .ad-card .ad-footer {
    border-radius: 10px !important;
    overflow: hidden;
}

/* Falls Bilder manchmal "eckig" bleiben */
.experience-section .card-img-wrapper {
    border-radius: 18px !important;
}


@media (max-width: 992px) {
    .experience-section .experience-card {
        height: 300px;
    }
    .experience-section .card-title-wrapper h4 {
        font-size: 1.1rem; /* Kleinere Schriftgröße auf Tablets */
    }
    .experience-section .card-title-wrapper {
        padding: 0.5rem; /* Noch kleinerer Padding */
    }
}

@media (max-width: 768px) {
    .experience-section .experience-card {
        height: 250px;
    }
    .experience-section .card-title-wrapper h4 {
        font-size: 1rem; /* Kleinere Schriftgröße auf Mobilgeräten */
    }
    .experience-section .card-title-wrapper {
        padding: 0.5rem;
    }
}
</style>
