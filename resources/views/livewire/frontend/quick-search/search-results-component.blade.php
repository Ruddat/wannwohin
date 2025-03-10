<div role="main" class="main">
    <section class="location-results py-5">
        <div class="container">

            <!-- Banner oberhalb der Filterleiste -->
            <div class="row mb-4">
                <div class="col-12">
                    <x-ad-block position="above-filter" class="full-width" />
                </div>
            </div>

            @if ($totalResults === 0)
                <div class="alert alert-warning">
                    @autotranslate('Keine Ergebnisse gefunden. Bitte starte die Suche erneut.', app()->getLocale())
                </div>
            @endif

            <!-- Sticky-Filterleiste nur für mobile Geräte -->
            <div class="d-flex justify-content-between align-items-center p-2 bg-white shadow-sm sticky-top d-md-none">
                <button class="btn btn-outline-danger filter-btn" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                    <i class="fas fa-sliders-h"></i> Filtern
                    <span class="badge bg-danger text-white">{{ $totalResults }}</span>
                </button>
                <div class="d-flex align-items-center gap-2">
                    <button wire:click="toggleSortDirection" class="btn btn-outline-secondary btn-sm shadow-sm" title="Aufsteigend sortieren">
                        <i class="fas fa-sort-amount-up" @if ($sortDirection === 'asc') style="color:#22c0e8;" @endif></i>
                    </button>
                    <button wire:click="toggleSortDirection" class="btn btn-outline-secondary btn-sm shadow-sm" title="Absteigend sortieren">
                        <i class="fas fa-sort-amount-down" @if ($sortDirection === 'desc') style="color:#22c0e8;" @endif></i>
                    </button>
                </div>
            </div>

            <!-- Filterbereich -->
            <div class="card shadow-sm mb-4 filter-section collapse d-md-block" id="filterCollapse">
                <div class="card-body">
                    <div class="bg-light p-3 rounded">
                        <h5 class="card-title">{{ $totalResults }} @autotranslate('Reiseziele wurden nach Deinen Kriterien gefunden', app()->getLocale())</h5>
                        <hr>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach ($activeFilters as $key => $value)
                                @if ($value)
                                    @if (is_array($value) && $key === 'spezielle')
                                        @foreach ($value as $item)
                                            <span class="badge bg-primary">
                                                {{ $this->getFilterLabel($key, $item) }}
                                                <button type="button" class="btn-close btn-close-white" wire:click.prevent="removeFilter('{{ $key }}', '{{ $item }}')"></button>
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-primary">
                                            {{ $this->getFilterLabel($key, $value) }}
                                            @if ($key !== 'urlaub')
                                                <button type="button" class="btn-close btn-close-white" wire:click="removeFilter('{{ $key }}')"></button>
                                            @endif
                                        </span>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded shadow-sm mt-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-list-ol"></i>
                            <label for="resultsPerPage" class="fw-bold mb-0">@autotranslate('Ergebnisse pro Seite:', app()->getLocale())</label>
                            <select wire:model.change="perPage" class="form-select form-select-sm w-auto shadow-sm">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label for="sortSelect" class="fw-bold mb-0">@autotranslate('Sortieren nach:', app()->getLocale())</label>
                            <select wire:model.change="sortBy" class="form-select form-select-sm w-auto shadow-sm">
                                <option value="price_flight">Preis</option>
                                <option value="title">Reiseziel</option>
                                <option value="climate_data->main->temp">Tagestemperatur</option>
                                <option value="continent_id">Kontinent</option>
                                <option value="country_id">Land</option>
                                <option value="flight_hours">Flugdauer</option>
                            </select>
                            <button wire:click="toggleSortDirection" class="btn btn-outline-secondary btn-sm shadow-sm d-none d-md-inline-flex" title="Aufsteigend sortieren">
                                <i class="fas fa-sort-amount-up" @if ($sortDirection === 'asc') style="color:#22c0e8;" @endif></i>
                            </button>
                            <button wire:click="toggleSortDirection" class="btn btn-outline-secondary btn-sm shadow-sm d-none d-md-inline-flex" title="Absteigend sortieren">
                                <i class="fas fa-sort-amount-down" @if ($sortDirection === 'desc') style="color:#22c0e8;" @endif></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @php
                // Inline-Werbung für die Timeline
                $locationsArray = $locations->toArray();
                $totalLocations = count($locationsArray);
                $ads = \App\Models\ModAdvertisementBlocks::where('is_active', 1)
                    ->where(function ($query) {
                        $query->whereJsonContains('position', 'inline-timeline')
                              ->orWhere('position', 'inline-timeline');
                    })
                    ->inRandomOrder()
                    ->limit(2)
                    ->get();

                // Sidebar-Werbung
                $sidebarAd = \App\Models\ModAdvertisementBlocks::where('is_active', 1)
                    ->where(function ($query) {
                        $query->whereJsonContains('position', 'sidebar-ad')
                              ->orWhere('position', 'sidebar-ad');
                    })
                    ->inRandomOrder()
                    ->first();

                $adCount = min(max(1, floor($totalLocations / 3)), $ads->count());
                $adPositions = [];
                if ($totalLocations > 0 && $adCount > 0) {
                    $step = max(2, floor($totalLocations / ($adCount + 1)));
                    for ($i = 0; $i < $adCount; $i++) {
                        $position = min($step * ($i + 1), $totalLocations - 1);
                        $adPositions[] = $position;
                    }
                }

                $adAssignments = [];
                foreach ($adPositions as $key => $position) {
                    $adAssignments[$position] = $ads[$key] ?? null;
                }
            @endphp

            <div class="row">
                <!-- Timeline -->
                <div class="{{ $sidebarAd ? 'col-12 col-md-8 col-lg-9' : 'col-12' }}">
                    <ul class="timeline">
                        @forelse($locations as $index => $location)
                            @if(array_key_exists($index, $adAssignments))
                                <!-- Inline-Werbung -->
                                <li>
                                    <div class="timeline-content">
                                        <div class="ad-card">
                                            <div class="ad-content">
                                                {!! $adAssignments[$index]->script !!}
                                            </div>
                                            <div class="ad-footer">
                                                <small class="text-muted">Werbung | Ad ID: {{ $adAssignments[$index]->id }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            <!-- Reiseziel -->
                            <li>
                                <div class="timeline-content">
                                    <div class="card-container">
                                        <!-- Image Section mit klickbarem Overlay -->
                                        <div class="card-image zoom-effect"
                                            style="background-image: url('{{ asset("{$location->text_pic1}") }}')">
                                            <a href="{{ route('location.details', [
                                                'continent' => $location->country->continent->alias,
                                                'country' => $location->country->alias,
                                                'location' => $location->alias,
                                            ]) }}"
                                                class="image-link"></a>
                                        </div>

                                        <!-- Content Section mit eigenem Link -->
                                        <a href="{{ route('location.details', [
                                            'continent' => $location->country->continent->alias,
                                            'country' => $location->country->alias,
                                            'location' => $location->alias,
                                        ]) }}"
                                            class="card-link">
                                            <div class="card-details">
                                                <!-- Header -->
                                                <div class="card-header">
                                                    <h4 class="location-title">{{ $location->title }}</h4>
                                                    @php
                                                        $germanMonths = [
                                                            1 => 'Januar',
                                                            2 => 'Februar',
                                                            3 => 'März',
                                                            4 => 'April',
                                                            5 => 'Mai',
                                                            6 => 'Juni',
                                                            7 => 'Juli',
                                                            8 => 'August',
                                                            9 => 'September',
                                                            10 => 'Oktober',
                                                            11 => 'November',
                                                            12 => 'Dezember',
                                                        ];
                                                        $displayMonth = isset($selectedMonth) && isset($germanMonths[$selectedMonth])
                                                            ? $germanMonths[$selectedMonth]
                                                            : 'Unbekannt';
                                                    @endphp
                                                    <span class="travel-month">im {{ $displayMonth }}</span>
                                                    <span class="price">ab
                                                        {{ number_format($location->price_flight, 0, ',', '.') }}
                                                        €</span>
                                                </div>
                                                <!-- Info Section -->
                                                <div class="card-info">
                                                    <div class="info-item">
                                                        <img src="{{ asset('assets/flags/4x3/' . strtolower($location->country->country_code) . '.svg') }}"
                                                            alt="Flag" class="flag-icon">
                                                        <span>{{ $location->country->title ?? 'Unbekanntes Land' }}</span>
                                                    </div>
                                                    @php
                                                        $continentIcons = [
                                                            'africa' => 'fas fa-globe-africa',
                                                            'asia' => 'fas fa-globe-asia',
                                                            'europe' => 'fas fa-globe-europe',
                                                            'north-america' => 'fas fa-globe-americas',
                                                            'south-america' => 'fas fa-globe-americas',
                                                            'oceania' => 'fas fa-globe',
                                                            'antarctica' => 'fas fa-snowflake',
                                                        ];
                                                        $continentAlias = strtolower($location->country->continent->alias ?? 'unknown');
                                                        $continentIcon = $continentIcons[$continentAlias] ?? 'fas fa-globe';
                                                    @endphp
                                                    <div class="info-item">
                                                        <i class="{{ $continentIcon }} text-black"></i>
                                                        <span class="ms-2">{{ $location->country->continent->title ?? 'Unbekannter Kontinent' }}</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <i class="fas fa-arrows-alt-h"></i>
                                                        <span>{{ number_format(round($location->flight_hours, 1), 1, ',', '.') }}
                                                            Flugstunden</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span>Tagsüber</span>
                                                        <span>
                                                            @php
                                                                $dayTemperature = $location->climate_data['main']['temp'] ?? null;
                                                                if (is_null($dayTemperature) && isset($location->historicalClimates)) {
                                                                    $lastHistoricalData = $location->historicalClimates->last();
                                                                    $dayTemperature = $lastHistoricalData
                                                                        ? $lastHistoricalData->temperature_avg
                                                                        : null;
                                                                }
                                                            @endphp
                                                            {{ is_numeric($dayTemperature) ? number_format($dayTemperature, 1, ',', '.') . '°C' : 'N/A' }}
                                                        </span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span>Wasser</span>
                                                        <span>
                                                            @php
                                                                $waterTemperature = $location->climate_data['water_temperature'] ?? null;
                                                                if (is_null($waterTemperature) && isset($location->historicalClimates)) {
                                                                    $lastHistoricalData = $location->historicalClimates->last();
                                                                    $waterTemperature = $lastHistoricalData
                                                                        ? $lastHistoricalData->temperature_avg
                                                                        : 'N/A';
                                                                }
                                                            @endphp
                                                            {{ is_numeric($waterTemperature) ? intval($waterTemperature) . '°C' : 'N/A' }}
                                                        </span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span>Sonne</span>
                                                        <span>
                                                            @php
                                                                $sunshine = $location->climate_data['sunshine_hours'] ?? null;
                                                            @endphp
                                                            {{ is_numeric($sunshine) ? number_format($sunshine, 1, ',', '.') . ' h' : 'N/A' }}
                                                        </span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span>Regentage</span>
                                                        <span>
                                                            @php
                                                                $rain = $location->climate_data['rain']['1h'] ?? null;
                                                                if (is_null($rain) && isset($location->historicalClimates)) {
                                                                    $lastHistoricalData = $location->historicalClimates->last();
                                                                    $rain = $lastHistoricalData
                                                                        ? $lastHistoricalData->precipitation
                                                                        : null;
                                                                }
                                                            @endphp
                                                            {{ is_numeric($rain) ? intval($rain) . ' Tage' : 'N/A' }}
                                                        </span>
                                                    </div>
                                                    @php
                                                        $decodedMonths = json_decode($location->best_traveltime_json, true);
                                                        $months = collect(is_array($decodedMonths) ? $decodedMonths : [])
                                                            ->filter(fn($month) => is_numeric($month) && $month >= 1 && $month <= 12)
                                                            ->map(fn($month) => (int) $month)
                                                            ->sort()
                                                            ->values();
                                                        $germanMonths = [
                                                            1 => 'Jan', 2 => 'Feb', 3 => 'Mär', 4 => 'Apr', 5 => 'Mai', 6 => 'Jun',
                                                            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Dez'
                                                        ];
                                                        if ($months->isEmpty()) {
                                                            $bestTravelMonths = 'Keine Empfehlung verfügbar';
                                                        } else {
                                                            $groupedMonths = [];
                                                            $tempGroup = [];
                                                            foreach ($months as $index => $month) {
                                                                if (empty($tempGroup) || end($tempGroup) == $month - 1) {
                                                                    $tempGroup[] = $month;
                                                                } else {
                                                                    $groupedMonths[] = $tempGroup;
                                                                    $tempGroup = [$month];
                                                                }
                                                            }
                                                            if (!empty($tempGroup)) {
                                                                $groupedMonths[] = $tempGroup;
                                                            }
                                                            $bestTravelMonths = collect($groupedMonths)
                                                                ->map(function ($group) use ($germanMonths) {
                                                                    return count($group) > 1
                                                                        ? $germanMonths[$group[0]] . ' - ' . $germanMonths[end($group)]
                                                                        : $germanMonths[$group[0]] ?? 'Unbekannter Monat';
                                                                })
                                                                ->implode(', ');
                                                        }
                                                    @endphp
                                                    <div class="info-item" wire:ignore>
                                                        <span>Beste Reisezeit</span>
                                                        <span> {{ $bestTravelMonths ?? 'N/A' }}</span>
                                                        <i class="fas fa-info-circle text-primary ms-1" data-bs-toggle="tooltip"
                                                            title="Empfohlene Monate für eine Reise"></i>
                                                    </div>
                                                </div>
                                                <!-- Icon-Leiste -->
                                                @php
                                                    $iconMap = [
                                                        'list_sports' => '<i class="fas fa-biking fa-lg me-1" title="Sport"></i>',
                                                        'list_island' => '<i class="fas fa-umbrella-beach fa-lg me-1" title="Insel"></i>',
                                                        'list_culture' => '<i class="fa fa-theater-masks fa-lg me-1" title="Kultur"></i>',
                                                        'list_nature' => '<i class="fas fa-tree fa-lg me-1" title="Natur"></i>',
                                                        'list_watersport' => '<i class="fas fa-swimmer fa-lg me-1" title="Wassersport"></i>',
                                                        'list_wintersport' => '<i class="fas fa-snowflake fa-lg me-1" title="Wintersport"></i>',
                                                        'list_mountainsport' => '<i class="fas fa-mountain fa-lg me-1" title="Bergsport"></i>',
                                                        'list_biking' => '<i class="fas fa-biking fa-lg me-1" title="Radfahren"></i>',
                                                        'list_fishing' => '<i class="fas fa-fish fa-lg me-1" title="Angeln"></i>',
                                                        'list_amusement_park' => '<i class="fas fa-ticket-alt fa-lg me-1" title="Freizeitpark"></i>',
                                                        'list_water_park' => '<i class="fas fa-water fa-lg me-1" title="Wasserpark"></i>',
                                                        'list_animal_park' => '<i class="fas fa-paw fa-lg me-1" title="Tierpark"></i>',
                                                    ];
                                                @endphp
                                                <div class="icon-bar" wire:ignore>
                                                    @foreach ($iconMap as $flag => $icon)
                                                        @if ($location->$flag)
                                                            <span class="tooltip-container"
                                                                data-tooltip="{{ strip_tags($icon) }}">
                                                                {!! $icon !!}
                                                                <div class="custom-tooltip">
                                                                    {{ ucfirst(explode('"', $icon)[3]) }}</div>
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <p class="text-center">@autotranslate('Keine Ergebnisse gefunden.', app()->getLocale())</p>
                        @endforelse
                    </ul>
                </div>

                <!-- Sticky Sidebar (rechts) -->
                @if($sidebarAd)
                    <div class="col-md-4 col-lg-3 sidebar-ad d-none d-md-block">
                        <div class="sticky-top" id="sidebar-ad-container">
                            {!! $sidebarAd->script !!}
                            <div class="ad-footer text-center mt-2">
                                <small class="text-muted">Werbung | Ad ID: {{ $sidebarAd->id }}</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Banner unterhalb der Timeline -->
            <div class="row mt-4">
                <div class="col-12">
                    <x-ad-block position="below-timeline" class="full-width" />
                </div>
            </div>

            <!-- Pagination -->
            @if ($locations instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="custom-pagination-container d-flex justify-content-center mt-4">
                    {{ $locations->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </section>

    <!-- Wellen-Animation am Ende der Ergebnisse -->
    <div class="waves">
        <div class="wave" id="wave1"></div>
        <div class="wave" id="wave2"></div>
        <div class="wave" id="wave3"></div>
        <div class="wave" id="wave4"></div>
    </div>


<!-- Bestehende Styles bleiben erhalten, neue Styles für Werbung hinzufügen -->
<style>
    /* Werbe-Styling */
    .ad-card {
        display: flex;
        flex-direction: column;
        background-color: #f8f9fa;
        padding: 15px;
        box-sizing: border-box;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

    /* Sidebar-Werbung */
    .sidebar-ad {
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 12px;
    }

    .sidebar-ad .sticky-top {
        top: 15px;
    }

    /* Full-Width Banner */
    .ad-card.full-width {
        height: auto;
        max-width: 100%;
    }

    /* Responsive Anpassungen */
    @media (max-width: 992px) {
        .sidebar-ad {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .ad-card.full-width {
            height: auto;
            padding: 5px;
        }
    }
</style>

<!-- Bestehende Skripte -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tooltip-container').forEach(function (tooltip) {
        tooltip.addEventListener('click', function (e) {
            e.stopPropagation();
            this.classList.toggle('clicked');
        });
    });

    window.addEventListener('click', function (e) {
        document.querySelectorAll('.tooltip-container').forEach(function (tooltip) {
            if (!tooltip.contains(e.target)) {
                tooltip.classList.remove('clicked');
            }
        });
    });

    // Höhe der Sidebar-Werbung anpassen
    const timelineContainer = document.querySelector('ul.timeline');
    const sidebarAdContainer = document.getElementById('sidebar-ad-container');
    if (timelineContainer && sidebarAdContainer) {
        const adjustHeight = () => {
            const timelineHeight = timelineContainer.offsetHeight;
            sidebarAdContainer.style.maxHeight = `${timelineHeight}px`;
            sidebarAdContainer.style.overflowY = 'auto';
        };
        adjustHeight();
        window.addEventListener('resize', adjustHeight);
    }

    const currentHour = new Date().getHours();
    const waves = document.querySelector('.waves');
    if (currentHour >= 6 && currentHour < 12) {
        waves.classList.add('morning');
    } else if (currentHour >= 12 && currentHour < 18) {
        waves.classList.add('afternoon');
    } else if (currentHour >= 18 && currentHour < 21) {
        waves.classList.add('evening');
    } else {
        waves.classList.add('night');
    }
});
</script>

<!-- Bestehende Styles bleiben unverändert -->
<style>
    ul.timeline {
        list-style-type: none;
        position: relative;
        padding: 0;
        margin: 0;
    }

    ul.timeline:before {
        content: '';
        background: #d4d9df;
        display: inline-block;
        position: absolute;
        left: 30px;
        width: 2px;
        height: 100%;
        z-index: 400;
    }

    ul.timeline>li {
        margin: 50px 0;
        padding-left: 60px;
        position: relative;
    }

    ul.timeline>li:before {
        content: '';
        background: white;
        display: inline-block;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        left: 21px;
        border-radius: 50%;
        border: 3px solid #22c0e8;
        width: 20px;
        height: 20px;
        z-index: 401;
        transition: background 0.3s ease;
    }

    ul.timeline>li:hover:before {
        background: #22c0e8;
    }

    .timeline-content {
        background: #fff;
        padding: 0;
        border-radius: 10px;
        box-shadow: none;
        transition: transform 0.2s ease-in-out;
        text-align: left;
    }

    ul.timeline>li:hover .timeline-content {
        transform: translateY(-5px);
    }

    @media (max-width: 768px) {
        ul.timeline {
            padding-left: 20px;
        }

        ul.timeline:before {
            left: 15px;
        }

        ul.timeline>li:before {
            left: 10px;
        }
    }

    @media (max-width: 576px) {
        ul.timeline:before {
            display: none;
        }

        ul.timeline>li:before {
            display: none;
        }

        ul.timeline>li {
            padding-left: 20px;
        }

        ul.timeline>li {
            padding-left: 0;
            text-align: center;
        }

        .timeline-content {
            margin: 0 auto;
            max-width: 90%;
            text-align: center;
        }
    }

    .card-container {
        display: flex;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .card-image {
        background-size: cover;
        background-position: center;
        width: 25%;
        min-height: 200px;
    }

    .card-details {
        width: 100%;
        padding: 20px;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .location-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #333;
    }

    .travel-month {
        font-size: 1rem;
        color: #666;
    }

    .price {
        font-size: 1.5rem;
        font-weight: bold;
        color: #000;
    }

    .card-info {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
        width: calc(50% - 10px);
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 5px;
    }

    .flag-icon {
        width: 24px;
        height: auto;
    }

    .icon-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
        padding-top: 10px;
        border-bottom: 1px solid #e0e0e0;
        justify-content: flex-start;
    }

    .icon-bar i,
    .icon-bar img {
        font-size: 1.5rem;
        color: #22c0e8;
        transition: transform 0.2s ease-in-out;
    }

    .icon-bar i:hover,
    .icon-bar img:hover {
        transform: scale(1.2);
        color: #1b9dbb;
    }

    .tooltip-container {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }

    .custom-tooltip {
        visibility: hidden;
        background-color: #333;
        color: #fff;
        text-align: center;
        padding: 5px 10px;
        border-radius: 5px;
        position: absolute;
        z-index: 1000;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        white-space: nowrap;
        opacity: 0;
        transition: opacity 0.3s ease, transform 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .custom-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #333 transparent transparent transparent;
    }

    .tooltip-container.clicked .custom-tooltip {
        visibility: visible;
        opacity: 1;
        transform: translateX(-50%) translateY(-10px);
    }

    .zoom-effect {
        background-size: cover;
        background-position: center;
        width: 50%;
        min-height: 200px;
        overflow: hidden;
        transition: transform 0.5s ease, box-shadow 0.3s ease;
        position: relative;
    }

    .zoom-effect:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }

    .zoom-effect::before {
        content: '';
        position: absolute;
        top: 0;
        left: -75%;
        width: 50%;
        height: 100%;
        background: linear-gradient(to right, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.1));
        transform: skewX(-25deg);
        transition: all 0.7s ease-in-out;
    }

    .zoom-effect:hover::before {
        left: 125%;
    }

    @media (max-width: 768px) {
        .zoom-effect {
            transform: none;
            box-shadow: none;
        }

        .zoom-effect:hover {
            transform: none;
        }

        .card-container {
            flex-direction: column;
        }

        .card-image {
            width: 100%;
            min-height: 150px;
        }

        .card-details {
            width: 100%;
        }
    }

    .custom-pagination-container nav {
        display: inline-flex;
        background-color: #fff;
        padding: 10px 20px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .custom-pagination-container .pagination .page-link {
        color: #22c0e8;
        border: none;
        margin: 0 5px;
        padding: 10px 15px;
        border-radius: 8px;
        transition: all 0.3s ease-in-out;
    }

    .custom-pagination-container .pagination .page-link:hover {
        background-color: #22c0e8;
        color: #fff;
        box-shadow: 0 4px 10px rgba(34, 192, 232, 0.4);
    }

    .custom-pagination-container .pagination .page-item.active .page-link {
        background-color: #1b9dbb;
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(27, 157, 187, 0.4);
        border: none;
    }

    .custom-pagination-container .pagination .page-item.disabled .page-link {
        color: #ccc;
        cursor: not-allowed;
        background-color: #f8f9fa;
    }

    @media (max-width: 576px) {
        .custom-pagination-container nav {
            padding: 5px 10px;
        }

        .custom-pagination-container .pagination .page-link {
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        .card-image {
            position: relative;
            background-size: cover;
            background-position: center;
            width: 100%;
            min-height: 200px;
            overflow: hidden;
            border-radius: 10px 0 0 10px;
        }
    }

    .image-link {
        display: block;
        width: 100%;
        height: 100%;
        text-decoration: none;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 2;
    }

    .card-link {
        display: block;
        text-decoration: none;
        color: inherit;
        transition: box-shadow 0.3s ease-in-out;
    }

    .card-link:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .card-link:focus,
    .card-link:active {
        outline: none;
        background-color: transparent;
    }

    .card-link .info-item,
    .card-link .location-title,
    .card-link .price {
        color: inherit;
    }

    .filter-btn {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 8px 12px;
        border-radius: 30px;
        font-weight: bold;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .filter-btn:hover {
        background-color: #dc3545;
        color: #fff;
    }

    .filter-btn .badge {
        margin-left: 5px;
    }

    .btn-outline-secondary {
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-outline-secondary:hover {
        background-color: #22c0e8;
        color: #fff;
    }

    .sticky-top {
        top: 0;
        z-index: 1;
    }

    @media (min-width: 768px) {
        .d-md-none {
            display: none !important;
        }

        .filter-section {
            display: block !important;
        }

        .filter-toggle {
            display: none;
        }
    }

    .waves {
        position: relative;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100px;
        overflow: hidden;
        margin: 0;
        padding: 0;
    }

    .wave {
        position: absolute;
        top: 0;
        left: 0;
        width: 200%;
        height: 100px;
        background: url("https://i.imgur.com/ZAts69f.png");
        background-size: 1000px 100px;
    }

    .wave#wave1 {
        opacity: 1;
        bottom: 0;
        animation: animateWaves 6s linear infinite;
    }

    .wave#wave2 {
        opacity: 0.5;
        bottom: 10px;
        animation: animate 6s linear infinite !important;
    }

    .wave#wave3 {
        opacity: 0.2;
        bottom: 15px;
        animation: animateWaves 5s linear infinite;
    }

    .wave#wave4 {
        opacity: 0.7;
        bottom: 20px;
        animation: animate 4s linear infinite;
    }

    @keyframes animateWaves {
        0% {
            background-position-x: 1000px;
        }
        100% {
            background-position-x: 0px;
        }
    }

    @keyframes animate {
        0% {
            background-position-x: -1000px;
        }
        100% {
            background-position-x: 0px;
        }
    }

    .morning {
        filter: hue-rotate(30deg) brightness(1.2);
    }

    .afternoon {
        filter: hue-rotate(200deg) brightness(1);
    }

    .evening {
        filter: hue-rotate(300deg) brightness(0.8);
    }

    .night {
        filter: hue-rotate(240deg) brightness(0.5);
    }

    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }

    .card-title {
        font-weight: 600;
        font-size: 1.2rem;
        margin-bottom: 1rem;
        color: #2c3e50;
    }

    .badge {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        border-radius: 20px;
        background-color: #3498db;
        color: rgb(54, 53, 53);
        margin: 0.25rem;
        display: inline-flex;
        align-items: center;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .badge:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
    }

    .btn-close {
        cursor: pointer;
        padding: 0.2rem;
        margin-left: 0.5rem;
        font-size: 0.65rem;
        background-color: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transition: background-color 0.3s ease;
    }

    .btn-close:hover {
        background-color: rgba(255, 255, 255, 0.5);
    }

    @media (max-width: 768px) {
        .card-title {
            font-size: 1rem;
        }

        .badge {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
        }

        .btn-close {
            padding: 0.15rem;
            font-size: 0.6rem;
        }

        .filter-container {
            flex-direction: column;
            align-items: flex-start;
        }

        .d-flex.flex-wrap {
            flex-direction: column;
            gap: 1rem;
        }
    }

    .bg-light {
        background-color: #f8f9fa !important;
        padding: 1rem;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .filter-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .result-form-select {
        border: 1px solid #ced4da;
        border-radius: 5px;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .result-form-select:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .btn-outline-primary {
        border: 1px solid #3498db;
        color: #3498db;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-outline-primary:hover {
        background-color: #3498db;
        color: white;
    }

    .fas {
        font-size: 1rem;
        color: #3498db;
    }
</style>
</div>
