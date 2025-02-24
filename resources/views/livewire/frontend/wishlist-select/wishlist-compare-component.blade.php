<div>
    @if(count($locations) > 0)
        <!-- Desktop-Ansicht: Tabelle -->
        <div class="card-body d-none d-md-block">
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th title="Land"><i class="fas fa-flag"></i></th>
                            <th title="Ort"><i class="fas fa-map-marker-alt"></i></th>
                            <th title="Preistendenz"><i class="fas fa-euro-sign"></i></th>
                            <th title="Flugzeit"><i class="fas fa-plane"></i></th>
                            <th title="Flugpreis ab"><i class="fas fa-money-bill-wave"></i></th>
                            <th title="Tagestemperatur"><i class="fas fa-thermometer-half"></i></th>
                            <th title="Sonnenstunden"><i class="fas fa-sun"></i></th>
                            <th title="Regentage"><i class="fas fa-cloud-rain"></i></th>
                            <th title="Löschen"><i class="fas fa-trash"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locations as $location)
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
            @foreach($locations as $location)
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
            @endforeach
            <button wire:click="clearCompare" class="btn btn-warning w-100 mb-3">
                Vergleichsliste leeren
            </button>
        </div>
    @else
        <div class="alert alert-info text-center">
            <p>Es wurden noch keine Standorte zum Vergleich hinzugefügt.</p>
        </div>
    @endif



<style>
/* Flaggen-Styling */
.flag-icon {
            width: 20px; /* Kleiner für Mobile */
            height: auto;
            border-radius: 3px;
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.2);
        }

        /* Mobile-Optimierung */
        @media (max-width: 767px) {
            .card-body {
                padding: 1rem; /* Weniger Padding auf Mobile */
            }
            .btn-sm {
                padding: 0.25rem 0.5rem; /* Kleinere Buttons */
                font-size: 0.875rem;
            }
            small {
                font-size: 0.75rem; /* Kleinere Schrift für Labels */
            }
        }

        /* Desktop-Tabelle */
        @media (min-width: 768px) {
            .flag-icon {
                width: 24px; /* Größer für Desktop */
            }
        }

/* 💰 Preistendenz: Farbpunkte */
.price-dot {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: grey;
}

/* 🔴 Teuer */
.price-level-3 { background-color: red; }

/* 🟠 Mittel */
.price-level-2 { background-color: orange; }

/* 🟢 Günstig */
.price-level-1 { background-color: green; }

/* 🟡 Unbekannt */
.price-level-0 { background-color: grey; }

</style>

</div>
