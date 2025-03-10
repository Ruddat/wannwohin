<div>

    <div role="main" class="main">
        <section class="location-results py-5">
            <div class="container">
                <!-- Banner oberhalb -->
                <div class="row mb-4">
                    <div class="col-12">
                        <x-ad-block position="above-compare" class="full-width" />
                    </div>
                </div>

                @php
                    $locationsArray = $locations->toArray();
                    $totalLocations = count($locationsArray);

                    // Alle Inline-Anzeigen außer Kiwi (ID 2)
                    $ads = \App\Models\ModAdvertisementBlocks::where('is_active', 1)
                        ->where(function ($query) {
                            $query->whereJsonContains('position', 'inline') // Prüft, ob 'inline' im Array enthalten ist
                                  ->orWhere('position', 'inline'); // Für alte String-Werte
                        })
                        ->where('id', '!=', 2)
                        ->inRandomOrder()
                        ->limit(2)
                        ->get();

                    // Kiwi-Widget separat holen
                    $kiwiAd = \App\Models\ModAdvertisementBlocks::where('is_active', 1)
                        ->where(function ($query) {
                            $query->whereJsonContains('position', 'kiwi-widget') // Prüft, ob 'kiwi-widget' im Array enthalten ist
                                  ->orWhere('position', 'kiwi-widget'); // Für alte String-Werte
                        })
                        ->where('id', 2)
                        ->first();

                    $availableAds = $ads->count();
                    $adCount = min(max(1, floor($totalLocations / 3)), $availableAds);

                    // Zufällige Positionen für Inline-Werbung
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

                @if(count($locations) > 0)
                    <div class="row">
                        <!-- Hauptinhalt -->
                        <div class="{{ $kiwiAd ? 'col-12 col-md-8 col-lg-9' : 'col-12' }}" id="compare-container-wrapper">
                            <!-- Desktop-Ansicht: Tabelle -->
                            <div class="card-body d-none d-md-block">
                                <div class="alert alert-info text-center mb-3">
                                    <i class="fas fa-info-circle"></i> Klicken Sie auf die Spaltenüberschriften, um die Tabelle nach dieser Spalte zu sortieren. Ein weiterer Klick kehrt die Sortierreihenfolge um.
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-center align-middle">
                                        <thead class="bg-light">
                                            <tr>
                                                <th wire:click="sortBy('iso2')" style="cursor: pointer;">
                                                    <i class="fas fa-flag"></i><br>
                                                    <small>Land</small>
                                                    @if($sortColumn === 'iso2')
                                                        <i class="fas {{ $sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }}"></i>
                                                    @endif
                                                </th>
                                                <th wire:click="sortBy('location_title')" style="cursor: pointer;">
                                                    <i class="fas fa-map-marker-alt"></i><br>
                                                    <small>Ort</small>
                                                    @if($sortColumn === 'location_title')
                                                        <i class="fas {{ $sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }}"></i>
                                                    @endif
                                                </th>
                                                <th wire:click="sortBy('price_trend')" style="cursor: pointer;">
                                                    <i class="fas fa-euro-sign"></i><br>
                                                    <small>Preistendenz</small>
                                                    @if($sortColumn === 'price_trend')
                                                        <i class="fas {{ $sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }}"></i>
                                                    @endif
                                                </th>
                                                <th wire:click="sortBy('flight_hours')" style="cursor: pointer;">
                                                    <i class="fas fa-plane"></i><br>
                                                    <small>Flugzeit</small>
                                                    @if($sortColumn === 'flight_hours')
                                                        <i class="fas {{ $sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }}"></i>
                                                    @endif
                                                </th>
                                                <th wire:click="sortBy('price_flight')" style="cursor: pointer;">
                                                    <i class="fas fa-money-bill-wave"></i><br>
                                                    <small>Flugpreis</small>
                                                    @if($sortColumn === 'price_flight')
                                                        <i class="fas {{ $sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }}"></i>
                                                    @endif
                                                </th>
                                                <th wire:click="sortBy('daily_temperature')" style="cursor: pointer;">
                                                    <i class="fas fa-thermometer-half"></i><br>
                                                    <small>Temperatur</small>
                                                    @if($sortColumn === 'daily_temperature')
                                                        <i class="fas {{ $sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }}"></i>
                                                    @endif
                                                </th>
                                                <th wire:click="sortBy('sunshine_per_day')" style="cursor: pointer;">
                                                    <i class="fas fa-sun"></i><br>
                                                    <small>Sonnenstunden</small>
                                                    @if($sortColumn === 'sunshine_per_day')
                                                        <i class="fas {{ $sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }}"></i>
                                                    @endif
                                                </th>
                                                <th wire:click="sortBy('rainy_days')" style="cursor: pointer;">
                                                    <i class="fas fa-cloud-rain"></i><br>
                                                    <small>Regentage</small>
                                                    @if($sortColumn === 'rainy_days')
                                                        <i class="fas {{ $sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down' }}"></i>
                                                    @endif
                                                </th>
                                                <th>
                                                    <i class="fas fa-trash"></i><br>
                                                    <small>Löschen</small>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($locations as $index => $location)
                                                <tr>
                                                    <td>
                                                        @if($location->iso2)
                                                            <img src="{{ asset('assets/flags/4x3/' . strtolower($location->iso2) . '.svg') }}"
                                                                 alt="{{ $location->iso2 }}" class="flag-icon">
                                                        @else
                                                            ❓
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('/details/' . $location->continent_alias . '/' . $location->country_alias . '/' . $location->location_alias) }}"
                                                           class="text-dark text-decoration-none">
                                                            {{ $location->location_title }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @if($location->price_trend)
                                                            @php
                                                                $trendColor = $location->price_trend === 'niedrig' ? '🟢' :
                                                                              ($location->price_trend === 'mittel' ? '🟡' : '🔴');
                                                            @endphp
                                                            {{ $trendColor }} {{ ucfirst($location->price_trend) }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>{{ $location->flight_hours ? intval($location->flight_hours) : 'N/A' }} h</td>
                                                    <td>{{ $location->price_flight !== null ? intval($location->price_flight) . ' €' : 'N/A' }}</td>
                                                    <td>{{ $location->daily_temperature ?? 'N/A' }}°C</td>
                                                    <td>{{ $location->sunshine_per_day ?? 'N/A' }}</td>
                                                    <td>{{ $location->rainy_days ?? 'N/A' }}</td>
                                                    <td>
                                                        <button wire:click="removeFromCompare({{ $location->location_id }})"
                                                                class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <!-- Inline-Werbung in der Tabelle -->
                                                @if(in_array($index + 1, $adPositions))
                                                    <tr class="ad-row">
                                                        <td colspan="9" class="p-3">
                                                            <div class="ad-content">
                                                                {!! $adAssignments[$index + 1]->script !!}
                                                            </div>
                                                            <div class="ad-footer text-center mt-2">
                                                                <small class="text-muted">Werbung | Ad ID: {{ $adAssignments[$index + 1]->id }}</small>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <button wire:click="clearCompare" class="btn btn-warning mt-3 w-100">
                                    Vergleichsliste leeren
                                </button>
                            </div>

                            <!-- Mobile-Ansicht: Karten -->
                            <div class="d-md-none">
                                @foreach($locations as $index => $location)
                                    <div class="card mb-3 shadow-sm">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="d-flex align-items-center">
                                                    @if($location->iso2)
                                                        <img src="{{ asset('assets/flags/4x3/' . strtolower($location->iso2) . '.svg') }}"
                                                             alt="{{ $location->iso2 }}" class="flag-icon me-2">
                                                    @else
                                                        <span class="me-2">❓</span>
                                                    @endif
                                                    <a href="{{ url('/details/' . $location->continent_alias . '/' . $location->country_alias . '/' . $location->location_alias) }}"
                                                       class="text-dark text-decoration-none fw-bold">
                                                        {{ $location->location_title }}
                                                    </a>
                                                </div>
                                                <button wire:click="removeFromCompare({{ $location->location_id }})"
                                                        class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                            <div class="row text-center">
                                                <div class="col-6 mb-2">
                                                    <small><i class="fas fa-euro-sign"></i> Preistendenz</small><br>
                                                    @if($location->price_trend)
                                                        @php
                                                            $trendColor = $location->price_trend === 'niedrig' ? '🟢' :
                                                                          ($location->price_trend === 'mittel' ? '🟡' : '🔴');
                                                        @endphp
                                                        <span>{{ $trendColor }} {{ ucfirst($location->price_trend) }}</span>
                                                    @else
                                                        N/A
                                                    @endif
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <small><i class="fas fa-plane"></i> Flugzeit</small><br>
                                                    {{ $location->flight_hours ? intval($location->flight_hours) : 'N/A' }} h
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <small><i class="fas fa-money-bill-wave"></i> Flugpreis</small><br>
                                                    {{ $location->price_flight !== null ? intval($location->price_flight) . ' €' : 'N/A' }}
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <small><i class="fas fa-thermometer-half"></i> Temperatur</small><br>
                                                    {{ $location->daily_temperature ?? 'N/A' }}°C
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <small><i class="fas fa-sun"></i> Sonnenstunden</small><br>
                                                    {{ $location->sunshine_per_day ?? 'N/A' }}
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <small><i class="fas fa-cloud-rain"></i> Regentage</small><br>
                                                    {{ $location->rainy_days ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Inline-Werbung in der Mobile-Ansicht -->
                                    @if(in_array($index + 1, $adPositions))
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <div class="card h-100 border-0 shadow-sm ad-card">
                                                    <div class="card-body p-0">
                                                        <div class="ad-content">
                                                            {!! $adAssignments[$index + 1]->script !!}
                                                        </div>
                                                        <div class="ad-footer text-center mt-2">
                                                            <small class="text-muted">Werbung | Ad ID: {{ $adAssignments[$index + 1]->id }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                <button wire:click="clearCompare" class="btn btn-warning w-100 mb-3">
                                    Vergleichsliste leeren
                                </button>
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
                            <x-ad-block position="below-compare" class="full-width" />
                        </div>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <p>Es wurden noch keine Standorte zum Vergleich hinzugefügt.</p>
                    </div>
                @endif
            </div>
        </section>
    </div>

<style>
/* Flaggen-Icons */
.flag-icon {
    width: 24px;
    height: 18px;
    vertical-align: middle;
}

/* Werbe-Styling */
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

/* Tabelle: Werbezeile */
.ad-row td {
    background-color: #f8f9fa;
    padding: 15px;
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

/* Für above-compare und below-compare */
.ad-card.full-width {
    height: auto;
    max-width: 100%;
}

/* Responsiv */
@media (max-width: 992px) {
    .kiwi-widget {
        display: none;
    }
    #compare-container-wrapper {
        width: 100%;
        max-width: 100%;
    }
}

@media (max-width: 768px) {
    .ad-card {
        padding: 5px;
    }
    .ad-card.full-width {
        height: auto;
        padding: 5px;
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // Höhe des Kiwi-Widgets an Compare-Container anpassen
    const compareContainer = document.getElementById('compare-container-wrapper');
    const kiwiContainer = document.getElementById('kiwi-widget-container');
    if (compareContainer && kiwiContainer) {
        const adjustHeight = () => {
            const compareHeight = compareContainer.offsetHeight;
            kiwiContainer.style.maxHeight = `${compareHeight}px`;
            kiwiContainer.style.overflowY = 'auto';
        };
        adjustHeight();
        window.addEventListener('resize', adjustHeight);
    }
});
</script>
</div>
