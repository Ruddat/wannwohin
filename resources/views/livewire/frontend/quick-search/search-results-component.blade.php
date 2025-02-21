    <div role="main" class="main">
            <section class="location-results py-5">
                <div class="container">
                    <h2 class="text-center mb-4">Reiseergebnisse</h2>

                    <div
                        class="d-flex flex-wrap justify-content-between align-items-center mb-4 bg-light p-3 rounded shadow-sm">
                        <!-- Ergebnisse pro Seite -->
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-list-ol text-primary"></i>
                            <label for="resultsPerPage" class="fw-semibold mb-0">Ergebnisse pro Seite:</label>
                            <select wire:model.change="perPage"
                                class="result-form-select form-select-sm w-auto shadow-sm">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <!-- Sortieren nach -->
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-sort text-primary"></i>
                            <label for="sortSelect" class="fw-semibold mb-0">Sortieren nach:</label>
                            <select wire:model.change="sortBy" class="result-form-select w-auto shadow-sm">
                                <option value="price_flight">Preis</option>
                                <option value="title">Reiseziel</option>
                                <option value="climate_data->main->temp">Tagestemperatur</option>
                                <option value="continent_id">Kontinent</option>
                                <option value="country_id">Land</option>
                                <option value="flight_hours">Flugdauer</option>
                            </select>

                            <!-- Sort Direction Buttons -->
                            <button wire:click="toggleSortDirection"
                                class="btn btn-outline-primary btn-sm border-0 shadow-sm">
                                <i class="fas fa-sort-amount-up"
                                    @if ($sortDirection === 'asc') style="color:#22c0e8;" @endif></i>
                            </button>

                            <button wire:click="toggleSortDirection"
                                class="btn btn-outline-primary btn-sm border-0 shadow-sm">
                                <i class="fas fa-sort-amount-down"
                                    @if ($sortDirection === 'desc') style="color:#22c0e8;" @endif></i>
                            </button>
                        </div>
                    </div>

                    <ul class="timeline">
                        @forelse($locations as $location)
                            <li>
                                <div class="timeline-content">
                                    <div class="card-container">
                                        <!-- Image Section -->

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

                                                        $displayMonth =
                                                            isset($selectedMonth) &&
                                                            isset($germanMonths[$selectedMonth])
                                                                ? $germanMonths[$selectedMonth]
                                                                : 'Unbekannt';
                                                    @endphp

                                                    <span class="travel-month">im {{ $displayMonth }}</span>
                                                    <span class="price">ab
                                                        {{ number_format($location->price_flight, 0, ',', '.') }}
                                                        €</span>
                                                </div>
                                        </a>

                                        <!-- Info Section -->
                                        <div class="card-info">
                                            <div class="info-item">
                                                <img src="{{ asset('assets/flags/4x3/' . strtolower($location->country->country_code) . '.svg') }}"
                                                    alt="Flag" class="flag-icon">
                                                <span>{{ $location->country->title ?? 'Unbekanntes Land' }}</span>
                                            </div>

                                            @php
                                            // Kontinent-Icons zuweisen
                                            $continentIcons = [
                                                'africa' => 'fas fa-globe-africa',
                                                'asia' => 'fas fa-globe-asia',
                                                'europe' => 'fas fa-globe-europe',
                                                'north-america' => 'fas fa-globe-americas',
                                                'south-america' => 'fas fa-globe-americas',
                                                'oceania' => 'fas fa-globe',
                                                'antarctica' => 'fas fa-snowflake',
                                            ];

                                            // Fallback Icon
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
                                                        // Temperatur aus aktuellen Klimadaten holen
                                                        $dayTemperature =
                                                            $location->climate_data['main']['temp'] ?? null;

                                                        // Fallback auf historische Daten
                                                        if (
                                                            is_null($dayTemperature) &&
                                                            isset($location->historicalClimates)
                                                        ) {
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
                                                        // Prüfen, ob aktuelle Daten verfügbar sind
                                                        $waterTemperature =
                                                            $location->climate_data['water_temperature'] ?? null;

                                                        // Fallback: Daten aus historischen Klimadaten holen
                                                        if (
                                                            is_null($waterTemperature) &&
                                                            isset($location->historicalClimates)
                                                        ) {
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
                                                        // Sonnenstunden aus den aktuellen Klimadaten holen
                                                        $sunshine = $location->climate_data['sunshine_per_day'] ?? null;

                                                        // Fallback auf historische Daten
                                                        if (
                                                            is_null($sunshine) &&
                                                            isset($location->historicalClimates)
                                                        ) {
                                                            $lastHistoricalData = $location->historicalClimates->last();
                                                            $sunshine = $lastHistoricalData
                                                                ? $lastHistoricalData->sunshine_hours
                                                                : null;
                                                        }
                                                    @endphp

                                                    {{ is_numeric($sunshine) ? number_format($sunshine, 1, ',', '.') . ' h' : 'N/A' }}
                                                </span>
                                            </div>

                                            <div class="info-item">
                                                <span>Regentage</span>
                                                <span>
                                                    @php
                                                        // Regentage aus aktuellen Klimadaten holen
                                                        $rain = $location->climate_data['rain']['1h'] ?? null;

                                                        // Fallback auf historische Daten
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
                                                // JSON in Array umwandeln
                                                $months = collect(json_decode($location->best_traveltime_json, true))
                                                    ->sort() // Sicherstellen, dass die Monate in richtiger Reihenfolge sind
                                                    ->map(function ($month) {
                                                        return (int) $month; // Integer-Werte für die Gruppierung
                                                    })
                                                    ->values();

                                                // Deutsche Monatsnamen
                                                $germanMonths = [
                                                    1 => 'Jan',
                                                    2 => 'Feb',
                                                    3 => 'Mär',
                                                    4 => 'Apr',
                                                    5 => 'Mai',
                                                    6 => 'Jun',
                                                    7 => 'Jul',
                                                    8 => 'Aug',
                                                    9 => 'Sep',
                                                    10 => 'Okt',
                                                    11 => 'Nov',
                                                    12 => 'Dez',
                                                ];

                                                // Gruppenbildung für zusammenhängende Monate
                                                $groupedMonths = [];
                                                $tempGroup = [];

                                                foreach ($months as $index => $month) {
                                                    if (empty($tempGroup) || end($tempGroup) == $month - 1) {
                                                        $tempGroup[] = $month; // Monat zur Gruppe hinzufügen
                                                    } else {
                                                        $groupedMonths[] = $tempGroup; // Vorherige Gruppe speichern
                                                        $tempGroup = [$month]; // Neue Gruppe starten
                                                    }
                                                }
                                                if (!empty($tempGroup)) {
                                                    $groupedMonths[] = $tempGroup; // Letzte Gruppe speichern
                                                }

                                                // Ausgabeformatierung: "Juni - August" oder "Juni"
                                                $bestTravelMonths = collect($groupedMonths)
                                                    ->map(function ($group) use ($germanMonths) {
                                                        return count($group) > 1
                                                            ? $germanMonths[$group[0]] .
                                                                    ' - ' .
                                                                    $germanMonths[end($group)]
                                                            : $germanMonths[$group[0]];
                                                    })
                                                    ->implode(', ');

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
                                                'list_sports' =>
                                                    '<i class="fas fa-biking fa-lg me-1" title="Sport"></i>',
                                                'list_island' =>
                                                    '<i class="fas fa-umbrella-beach fa-lg me-1" title="Insel"></i>',
                                                'list_culture' =>
                                                    '<i class="fa fa-theater-masks fa-lg me-1" title="Kultur"></i>',
                                                'list_nature' => '<i class="fas fa-tree fa-lg me-1" title="Natur"></i>',
                                                'list_watersport' =>
                                                    '<i class="fas fa-swimmer fa-lg me-1" title="Wassersport"></i>',
                                                'list_wintersport' =>
                                                    '<i class="fas fa-snowflake fa-lg me-1" title="Wintersport"></i>',
                                                'list_mountainsport' =>
                                                    '<i class="fas fa-mountain fa-lg me-1" title="Bergsport"></i>',
                                                'list_biking' =>
                                                    '<i class="fas fa-biking fa-lg me-1" title="Radfahren"></i>',
                                                'list_fishing' =>
                                                    '<i class="fas fa-fish fa-lg me-1" title="Angeln"></i>',
                                                'list_amusement_park' =>
                                                    '<i class="fas fa-ticket-alt fa-lg me-1" title="Freizeitpark"></i>',
                                                'list_water_park' =>
                                                    '<i class="fas fa-water fa-lg me-1" title="Wasserpark"></i>',
                                                'list_animal_park' =>
                                                    '<i class="fas fa-paw fa-lg me-1" title="Tierpark"></i>',
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
                                </div>
                </div>
                </li>
            @empty
                <p class="text-center">Keine Ergebnisse gefunden.</p>
                @endforelse
                </ul>
                <!-- Pagination -->
                <div class="custom-pagination-container d-flex justify-content-center mt-4">
                    {{ $locations->onEachSide(1)->links() }}
                </div>

            </section>
    </div>


    <!-- ✅ HTML: Rein CSS-basierte Timeline -->

    <style>
        ul.timeline {
            list-style-type: none;
            position: relative;
            padding: 0;
            margin: 0;
        }

        /* Linie links platzieren */
        ul.timeline:before {
            content: '';
            background: #d4d9df;
            display: inline-block;
            position: absolute;
            left: 30px;
            /* Linie links positionieren */
            width: 2px;
            height: 100%;
            z-index: 400;
        }

        /* Eintrag-Elemente */
        ul.timeline>li {
            margin: 50px 0;
            padding-left: 60px;
            /* Abstand zur Linie */
            position: relative;
        }

        /* Punkte mittig auf der Linie */
        ul.timeline>li:before {
            content: '';
            background: white;
            display: inline-block;
            position: absolute;
            top: 50%;
            /* Punkt mittig platzieren */
            transform: translateY(-50%);
            left: 21px;
            /* Abstand zur Linie */
            border-radius: 50%;
            border: 3px solid #22c0e8;
            width: 20px;
            height: 20px;
            z-index: 401;
            transition: background 0.3s ease;
        }

        /* Hover-Effekt für Punkte */
        ul.timeline>li:hover:before {
            background: #22c0e8;
        }

        /* Inhalt der Timeline-Boxen */
        /* Inhalt der Timeline-Boxen */
        .timeline-content {
            background: #fff;
            padding: 0;
            /* Entfernt zusätzliches Padding */
            border-radius: 10px;
            box-shadow: none;
            /* Entfernt den Schatten hier */
            transition: transform 0.2s ease-in-out;
            text-align: left;
        }

        /* Hover-Effekt für die Boxen */
        ul.timeline>li:hover .timeline-content {
            transform: translateY(-5px);
        }

        /* Responsive Anpassung */
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

        /* Timeline auf kleinen Geräten anpassen */
        @media (max-width: 576px) {
            ul.timeline:before {
                display: none;
                /* Linie ausblenden */
            }

            ul.timeline>li:before {
                display: none;
                /* Punkte ausblenden */
            }

            ul.timeline>li {
                padding-left: 20px;
                /* Reduziere den linken Abstand */
            }
        }

        /* Inhalt zentrieren auf kleinen Geräten */
        @media (max-width: 576px) {
            ul.timeline>li {
                padding-left: 0;
                /* Kein Abstand mehr links */
                text-align: center;
                /* Text zentrieren */
            }

            .timeline-content {
                margin: 0 auto;
                /* Zentriert die Box */
                max-width: 90%;
                /* Begrenzte Breite für bessere Lesbarkeit */
                text-align: center;
                /* Text innerhalb der Box zentrieren */
            }
        }
    </style>
    <style>
        /* Card-Container */
        .card-container {
            display: flex;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            /* Schatten bleibt hier */
            overflow: hidden;
            margin-bottom: 30px;
        }

        /* Image Section */
        .card-image {
            background-size: cover;
            background-position: center;
            width: 25%;
            min-height: 200px;
        }

        /* Content Section */
        .card-details {
            width: 75%;
            padding: 20px;
        }

        /* Header Styling */
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

        /* Info Section */
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

        /* Icon-Bar unter der Card-Info */
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

        /* Hover-Effekt für Icons */
        .icon-bar i:hover,
        .icon-bar img:hover {
            transform: scale(1.2);
            color: #1b9dbb;
        }

        .result-form-select {
            width: 100%;
            padding: 0.5rem;
            /* margin-top: 0.5rem; */
        }

        /* Tooltip-Container */


        /* Zoom-Effekt für das Bild */
        .zoom-effect {
            background-size: cover;
            background-position: center;
            width: 25%;
            min-height: 200px;
            overflow: hidden;
            transition: transform 0.5s ease, box-shadow 0.3s ease;
            position: relative;
        }

        /* Hover-Zoom */
        .zoom-effect:hover {
            transform: scale(1.1);
            /* Zoom-In-Effekt */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            /* Leichter Schatten für Tiefe */
            border-radius: 10px;
        }

        /* Optional: Schimmernder Lichtstrahl beim Hover */
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

        /* Schimmer-Effekt beim Hover */
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
        }



        /* Responsive Design */
        @media (max-width: 768px) {
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
    </style>

    <style>
        /* Tooltip-Container */
        .tooltip-container {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        /* Tooltip-Box */
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
            /* Über dem Icon */
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        /* Tooltip-Pfeil */
        .custom-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            /* Unter dem Tooltip */
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #333 transparent transparent transparent;
        }

        /* Sichtbar beim Klicken */
        .tooltip-container.clicked .custom-tooltip {
            visibility: visible;
            opacity: 1;
            transform: translateX(-50%) translateY(-10px);
        }
    </style>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tooltip-container').forEach(function (tooltip) {
        // Tooltip öffnen/schließen beim Klick
        tooltip.addEventListener('click', function (e) {
            e.stopPropagation(); // Verhindert das Schließen beim Klick auf das Tooltip-Element selbst
            this.classList.toggle('clicked');
        });
    });

    // Tooltip schließen, wenn außerhalb geklickt wird
    window.addEventListener('click', function (e) {
        document.querySelectorAll('.tooltip-container').forEach(function (tooltip) {
            if (!tooltip.contains(e.target)) {
                tooltip.classList.remove('clicked');
            }
        });
    });
});

    </script>


    <style>
        /* Pagination-Container */
        .custom-pagination-container nav {
            display: inline-flex;
            background-color: #fff;
            padding: 10px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Standard-Links */
        .custom-pagination-container .pagination .page-link {
            color: #22c0e8;
            /* Hauptfarbe */
            border: none;
            margin: 0 5px;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.3s ease-in-out;
        }

        /* Hover-Effekt */
        .custom-pagination-container .pagination .page-link:hover {
            background-color: #22c0e8;
            color: #fff;
            box-shadow: 0 4px 10px rgba(34, 192, 232, 0.4);
        }

        /* Aktive Seite */
        .custom-pagination-container .pagination .page-item.active .page-link {
            background-color: #1b9dbb;
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(27, 157, 187, 0.4);
            border: none;
        }

        /* Disabled-Links */
        .custom-pagination-container .pagination .page-item.disabled .page-link {
            color: #ccc;
            cursor: not-allowed;
            background-color: #f8f9fa;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .custom-pagination-container nav {
                padding: 5px 10px;
            }

            .custom-pagination-container .pagination .page-link {
                padding: 8px 12px;
                font-size: 0.9rem;
            }
        }
    </style>



    <style>
        /* Klickbarer Bereich für das Bild */
        .image-link {
            display: block;
            width: 100%;
            height: 100%;
            text-decoration: none;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
            /* Sicherstellen, dass der Link über dem Bild liegt */
        }

        /* Die Bild-Section bleibt sichtbar */
        .card-image {
            position: relative;
            background-size: cover;
            background-position: center;
            width: 30%;
            min-height: 200px;
            overflow: hidden;
            border-radius: 10px 0 0 10px;
        }

        /* Zoom-Effekt für das Bild */
        .card-image.zoom-effect:hover {
            transform: scale(1.1);
            transition: transform 0.5s ease;
        }

        /* Der gesamte Card-Bereich wird klickbar */
        .card-link {
            display: block;
            text-decoration: none;
            color: inherit;
            transition: box-shadow 0.3s ease-in-out;
        }

        /* Hover-Effekt */
        .card-link:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* Entfernen von Fokus- und Klickeffekten auf Mobil */
        .card-link:focus,
        .card-link:active {
            outline: none;
            background-color: transparent;
        }

        /* Icon und Text normal bleiben */
        .card-link .info-item,
        .card-link .location-title,
        .card-link .price {
            color: inherit;
        }
    </style>



    </div>
