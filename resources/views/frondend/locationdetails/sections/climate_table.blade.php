<section class="timeline-box right custom-box-shadow-2 box-shadow-2 py-4">
    <div class="container" style="background-color: #eaeff5; border-radius: 8px; padding: 20px;">
        <div class="row">
            <!-- Wetterinformationen -->
            <div class="experience-info col-lg-3 col-sm-5 bg-info p-3 rounded-start">
                <div class="text-center align-middle mb-4 pt-4">
                    <h4 class="text-color-white">
                        <i title="Wetter" class="fas fa-sun pe-1"></i> Wetter in {{ $location->continent->alias }}
                    </h4>
                </div>

                <div class="text-center align-middle mb-5">
                    <h1 class="text-color-white">
                        <img src="{{ $weather_data['icon'] ?? 'default-icon.png' }}" alt="Wetter-Icon" class="pe-2">
                        {{ $weather_data['temperature'] ?? 'N/A' }} °C
                    </h1>
                </div>

                <div class="text-center align-middle mb-5">
                    <h3 class="text-color-white">
                        {{ $weather_data['description'] ?? 'Keine Daten verfügbar' }}
                    </h3>
                </div>

                <div class="text-center align-middle">
                    <h5 class="text-color-white">
                        <span class="me-1">Luftfeuchtigkeit:</span>
                        {{ $weather_data['humidity'] ?? 'N/A' }} %
                    </h5>
                </div>
                <div class="text-center align-middle mb-1">
                    <h5 class="text-color-white">
                        <span class="me-1">Bewölkung:</span>
                        {{ $weather_data['cloudiness'] ?? 'N/A' }} %
                    </h5>
                </div>
                <div class="text-center align-middle mb-1">
                    <h5 class="text-color-white">
                        <span class="me-1">Wind ({{ $weather_data['wind_direction'] ?? '-' }}):</span>
                        {{ $weather_data['wind_speed'] ?? 'N/A' }} km/h
                    </h5>
                </div>
            </div>

            <!-- Klimatabelle -->
            <div class="experience-description col-lg-9 col-sm-7 bg-color-light px-3 py-3 rounded-end">
                <h4 class="text-color-dark font-weight-semibold mb-4">Klimatabelle {{ $location->title }}</h4>

                <table class="table table-striped table-bordered table-hover table-condensed location-climate-table climate-table mb-4">
                    <thead>
                        <tr>
                            <th class="center"><i alt="Monat" title="Monat" class="far fa-calendar-alt"></i></th>
                            <th class="center"><i class="fas fa-cloud-sun"></i></th>
                            <th class="center"><i class="fas fa-cloud-moon"></i></th>
                            @if ($climates->first()?->water_temperature_avg > 1)
                                <th class="center"><i title="Wassertemperatur" class="fas fa-water"></i></th>
                            @endif
                            <th class="center"><i title="Luftfeuchtigkeit" class="fas fa-tint"></i></th>
                            <th class="center"><i class="fas fa-sun"></i></th>
                            <th class="center"><i class="fas fa-umbrella"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($climates as $climate)
                            <tr>
                                <td class="center">{{ $climate->month }}</td>
                                <td class="center">{{ number_format($climate->daily_temperature, 1, ',', '.') }} °C</td>
                                <td class="center">{{ number_format($climate->night_temperature, 1, ',', '.') }} °C</td>
                                @if ($climates->first()?->water_temperature_avg > 1)
                                    <td class="center">{{ number_format($climate->water_temperature, 1, ',', '.') }} °C</td>
                                @endif
                                <td class="center">{{ $climate->humidity ? number_format($climate->humidity, 1, ',', '.').' %' : '-' }}</td>
                                <td class="center">{{ number_format($climate->sunshine_per_day, 1, ',', '.') }} h</td>
                                <td class="center">{{ number_format($climate->rainy_days, 1, ',', '.') }} t</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Button unter der Tabelle -->
                <div class="text-start">
                    <a class="btn btn-primary" target="_blank" href="https://www.klimatabelle.de/klima/{{ $location->continent->alias }}/{{ $location->country->alias }}/klimatabelle-{{ $location->alias }}.htm">
                        Mehr zu Klima & Wetter
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
