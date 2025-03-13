<section class="section section-no-border bg-color-light m-0 pb-4" style="background-color: #eaeff5 !important;">
    <div class="container">
        <h1 class="text-center mb-4">@autotranslate("Locations in", app()->getLocale()) {{ $country->title }}</h1>

        <!-- Banner oberhalb -->
        <div class="row mb-4">
            <div class="col-12">
                <x-ad-block position="above-locations" class="full-width" />
            </div>
        </div>

        @php
        $locationsArray = $locations->toArray();
        $totalLocations = count($locationsArray);

        // Alle Inline-Anzeigen
        $ads = \App\Models\ModAdvertisementBlocks::where('is_active', 1)
            ->where(function ($query) {
                $query->whereJsonContains('position', 'inline') // Prüft, ob 'inline' im Array enthalten ist
                      ->orWhere('position', 'inline'); // Für alte String-Werte
            })
            ->inRandomOrder()
            ->limit(2)
            ->get();

        // Kiwi-Widget holen (zufällig, nur nach Position 'kiwi-widget')
        $kiwiAd = \App\Models\ModAdvertisementBlocks::where('is_active', 1)
            ->where(function ($query) {
                $query->whereJsonContains('position', 'kiwi-widget') // Prüft, ob 'kiwi-widget' im Array enthalten ist
                      ->orWhere('position', 'kiwi-widget'); // Für alte String-Werte
            })
            ->inRandomOrder()
            ->first();

        // Debugging: Prüfe das Ergebnis (entferne nach Test)
        // dd($kiwiAd);

        $availableAds = $ads->count();
        $adCount = min(max(1, floor($totalLocations / 3)), $availableAds);

        // Zufällige Positionen für Inline-Werbung (zwischen Locations)
        $adPositions = $totalLocations > 0 && $adCount > 0
            ? array_rand(range(1, $totalLocations - 1), $adCount)
            : [];
        $adPositions = is_array($adPositions) ? $adPositions : [$adPositions];
        sort($adPositions);

        $adAssignments = [];
        foreach ($adPositions as $key => $position) {
            $adAssignments[$position] = $ads[$key] ?? null;
        }
    @endphp

        <div class="row g-4">
            <!-- Locations -->
            <div class="{{ $kiwiAd ? 'col-12 col-md-8 col-lg-9' : 'col-12' }}" id="locations-container-wrapper">
                <div class="row g-4" id="locations-container">
                    @foreach($locations as $index => $location)
                        <!-- Location-Kachel -->
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3" data-aos="fade-up">
                            <div class="card h-100 border-0 shadow-sm custom-card position-relative">
                                <!-- Bearbeiten-Button (nur für Admins sichtbar) -->
                                @if(Auth::guard('admin')->check())
                                    <a href="{{ route('location-manager.edit', $location->id) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-warning position-absolute top-0 end-40 m-2 card-edit-button">
                                        <i class="ti ti-edit"></i> @autotranslate('Bearbeiten', app()->getLocale())
                                    </a>
                                @endif
                                <!-- Wishlist-Button oben rechts als Overlay -->
                                <div class="wishlist-overlay1">
                                    @livewire('frontend.wishlist-select.wishlist-button-component', ['locationId' => $location->id])
                                </div>
                                <!-- Bild -->
                                <a href="{{ route('location.details', [
                                        'continent' => $location->country->continent->alias,
                                        'country' => $location->country->alias,
                                        'location' => $location->alias,
                                    ]) }}"
                                   class="text-decoration-none">
                                   <div class="card-img-wrapper">
                                    <img src="{{ $location->primaryImage() ?: asset('img/placeholders/location-placeholder.jpg') }}"
                                         class="card-img-top"
                                         alt="@autotranslate($location->title, app()->getLocale())">
                                </div>
                                </a>
                                <!-- Inhalt -->
                                <div class="card-body d-flex flex-column">
                                    <!-- Titel -->
                                    <h5 class="card-title text-truncate text-center">{{ $location->title }}</h5>
                                    <!-- Text -->
                                    <p class="card-text">
                                        @if (!empty($location->text_short))
                                            @autotranslate(Str::limit(strip_tags($location->text_short), 150), app()->getLocale())
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Inline-Werbe-Kachel nach der Location -->
                        @if(in_array($index + 1, $adPositions))
                            <div class="col-12 col-sm-12 col-md-8 col-lg-6" data-aos="fade-up">
                                <div class="card h-100 border-0 shadow-sm custom-card ad-card">
                                    <div class="card-body p-0">
                                        <div class="ad-content">
                                            {!! $adAssignments[$index + 1]->script !!}
                                        </div>
                                        <div class="ad-footer">
                                            <small class="text-muted">Werbung | Ad ID: {{ $adAssignments[$index + 1]->id }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
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
                <x-ad-block position="below-locations" class="full-width" />
            </div>
        </div>

        <div class="mb-4"></div>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    AOS.init({
        duration: 400,
        delay: 0,
        once: true
    });

    // Höhe des Kiwi-Widgets an Locations-Container anpassen
    const locationsContainer = document.getElementById('locations-container');
    const kiwiContainer = document.getElementById('kiwi-widget-container');
    if (locationsContainer && kiwiContainer) {
        const adjustHeight = () => {
            const locationsHeight = locationsContainer.offsetHeight;
            kiwiContainer.style.maxHeight = `${locationsHeight}px`;
            kiwiContainer.style.overflowY = 'auto';
        };
        adjustHeight();
        window.addEventListener('resize', adjustHeight);
    }
});
</script>

<style>
/* Allgemeine Karte */
.custom-card {
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    padding-bottom: 1rem;
}

.custom-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

/* Bildbereich */
.card-img-wrapper {
    height: 200px;
    overflow: hidden;
    border-bottom: 2px solid #ddd;
}

.card-img-top {
    height: 100%;
    width: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.custom-card:hover .card-img-top {
    transform: scale(1.1);
}

/* Karte: Body */
.card-body {
    flex-grow: 1;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Titel und Text */
.card-title {
    font-size: 1.1rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 0.5rem;
}

.card-text {
    color: #555;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    height: auto;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

/* Hover-Text und Link */
.custom-card a {
    color: inherit;
}

.custom-card a:hover {
    text-decoration: none;
    color: #007bff;
}

/* Bearbeiten-Button mit Hover-Effekt */
.card-edit-button {
    transition: opacity 0.3s ease;
    z-index: 10;
    width: fit-content;
}

.custom-card:hover .card-edit-button {
    opacity: 1;
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
    width: 100%;
    height: auto;
    max-height: 100%;
    object-fit: contain;
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

/* Für above-locations und below-locations */
.ad-card.full-width {
    height: auto;
    max-width: 100%;
}

/* Responsiv */
@media (max-width: 992px) {
    .kiwi-widget {
        display: none;
    }
    #locations-container-wrapper {
        width: 100%;
        max-width: 100%;
    }
}

@media (max-width: 768px) {
    .card-img-wrapper {
        height: 150px;
    }
    .card-title {
        font-size: 1rem;
    }
    .card-text {
        -webkit-line-clamp: 2;
    }
    .ad-card {
        padding: 5px;
    }
    .ad-card.full-width {
        height: auto;
        padding: 5px;
    }
}

/* Wishlist-Overlay oben rechts */
.wishlist-overlay1 {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
}
</style>
