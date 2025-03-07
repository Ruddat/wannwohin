<div role="main" class="main">
    <section class="location-results py-5">
        <div class="container">
            @if(count($locations) > 0)
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
        </div>
    </section>

</div>
