<div>
    @if(count($locations) > 0)

            <div class="card-body">
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
                                    <!-- 🌍 Flagge -->
                                    <td>
                                        @if($location->iso2)
                                            <img src="{{ asset('assets/flags/4x3/' . strtolower($location->iso2) . '.svg') }}"
                                                 alt="{{ $location->iso2 }}" class="flag-icon">
                                        @else
                                            ❓
                                        @endif
                                    </td>

                                    <!-- 📍 Name mit Link -->
                                    <td>
                                            <a href="{{ url('/details/' . $location->continent_alias . '/' . $location->country_alias . '/' . $location->location_alias) }}"


                                            class="text-dark text-decoration-none">
                                            {{ $location->location_title }}
                                        </a>
                                    </td>

                                    <!-- 💰 Preistendenz (Farbig) -->
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

                                    <!-- ✈️ Flugzeit -->
                                    <td>
                                        {{ $location->flight_hours ? intval($location->flight_hours) : 'N/A' }} h
                                    </td>

                                    <!-- 💵 Flugpreis -->
                                    <td>
                                        {{ $location->price_flight !== null ? intval($location->price_flight) . ' €' : 'N/A' }}
                                    </td>

                                    <!-- ☀️ Tagestemperatur -->
                                    <td>
                                        {{ $location->daily_temperature ?? 'N/A' }}°C
                                    </td>

                                    <!-- 🌞 Sonnenstunden -->
                                    <td>
                                        {{ $location->sunshine_per_day ?? 'N/A' }}
                                    </td>

                                    <!-- 🌧️ Regentage -->
                                    <td>
                                        {{ $location->rainy_days ?? 'N/A' }}
                                    </td>

                                    <!-- 🗑️ Löschen -->
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
                <button wire:click="clearCompare" class="btn btn-warning mt-3">
                    Vergleichsliste leeren
                </button>
            </div>

    @else
        <div class="alert alert-info text-center">
            <p>Es wurden noch keine Standorte zum Vergleich hinzugefügt.</p>
        </div>
    @endif



<style>
    /* 🌍 Flaggen-Styling */
.flag-icon {
    width: 24px;
    height: auto;
    border-radius: 3px;
    box-shadow: 0 0 3px rgba(0, 0, 0, 0.2);
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
