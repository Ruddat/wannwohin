
<section class="timeline-box right custom-box-shadow-2 box-shadow-2 py-4">
    <div class="container" style="background-color: #eaeff5; border-radius: 8px; padding: 20px;">
        <div class="row">
            <!-- Wetterinformationen -->
            <div class="experience-info col-lg-3 col-sm-5 bg-info p-3 rounded-start weather-box">
                <div class="text-center align-middle mb-4 pt-4">
                    <h4 class="text-color-white">
                        Wetter {{ $location->title }}
                    </h4>
                </div>

                <div class="text-center align-middle mb-5">
                    <h1 class="text-color-white">
                        <img src="{{ \App\Helpers\WeatherHelper::getWeatherIcon($weather_data['icon'] ?? null) }}" alt="Wetter-Icon" class="pe-2">
                        {{ \App\Helpers\WeatherHelper::formatTemperature($weather_data['temperature'] ?? null) }}
                    </h1>
                </div>
@php
  // dd($weather_data);
@endphp

<div class="text-center align-middle mb-5">
    <h3 class="text-color-white">
        @if (!empty($weather_data['description']))
        Beschreibung
        @else
            @autotranslate('Nicht verfügbar', app()->getLocale())
        @endif
    </h3>
</div>

                <div class="text-center align-middle">
                    <h5 class="text-color-white">
                        <span class="me-1">@autotranslate('Luftfeuchtigkeit:', app()->getLocale())</span>
                        {{ $weather_data['humidity'] ?? 'N/A' }} %
                    </h5>
                </div>
                <div class="text-center align-middle mb-1">
                    <h5 class="text-color-white">
                        <span class="me-1">@autotranslate('Bewölkung:', app()->getLocale())</span>
                        {{ $weather_data['cloudiness'] ?? 'N/A' }} %
                    </h5>
                </div>
                <div class="text-center align-middle mb-1">
                    <h5 class="text-color-white">
                        <span class="me-1">@autotranslate('Wind', app()->getLocale())  ({{ isset($weather_data['wind_direction']) ? \App\Helpers\WeatherHelper::getWindDirection($weather_data['wind_direction']) : 'Keine Daten' }}):</span>
                        {{ \App\Helpers\WeatherHelper::formatWindSpeed($weather_data['wind_speed'] ?? null) }}
                        @if(isset($weather_data['wind_speed']))
                            <small>({{ \App\Helpers\WeatherHelper::getWindDescription($weather_data['wind_speed']) }})</small>
                        @endif
                    </h5>
                </div>



            </div>

            <!-- Klimatabelle -->
            <div class="experience-description col-lg-9 col-sm-7 bg-color-light px-3 py-3 rounded-end">
                <h4 class="text-color-dark font-weight-semibold mb-4">Klimatabelle {{ $location->title }}</h4>

                <table class="table table-striped table-bordered table-hover table-condensed location-climate-table climate-table mb-4">
                    <thead>
                        <tr>
                            <th class="center">
                                <i alt="@autotranslate('Monat', app()->getLocale())" title="@autotranslate('Monat', app()->getLocale())" class="far fa-calendar-alt"></i>
                            </th>
                            <th class="center"><i class="fas fa-cloud-sun" title="@autotranslate('Tagesdurchschnittstemperatur', app()->getLocale())"></i></th>
                            <th class="center"><i class="fas fa-cloud-moon" title="@autotranslate('Nachtdurchschnittstemperatur', app()->getLocale())"></i></th>
                            @if ($climates->first()?->water_temperature_avg > 1)
                                <th class="center">
                                    <i title="@autotranslate('Wassertemperatur', app()->getLocale())" class="fas fa-water"></i>
                                </th>
                            @endif
                            <th class="center"><i title="@autotranslate('Luftfeuchtigkeit', app()->getLocale())" class="fas fa-tint"></i></th>
                            <th class="center"><i title="@autotranslate('Sonnenstunden', app()->getLocale())" class="fas fa-sun"></i></th>
                            <th class="center"><i title="@autotranslate('Regentage', app()->getLocale())" class="fas fa-umbrella"></i></th>
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

            <!-- Wettervorhersage -->
            <div class="container">
                <h4 class="text-color-dark font-weight-semibold mb-4">7-Tage-Wettervorhersage für {{ $location->title }}</h4>

                <div class="weather-forecast">
                    @foreach ($forecast as $day)
                        <div class="weather-forecast-day">
                            <p class="fw-bold">{{ $day['date'] }}</p>
                            <p class="fs-2">{{ $day['icon'] }}</p>
                            <p class="weather-description">{{ $day['weather'] }}</p>
                            <p class="temp-text">🌡️ Max: {{ $day['temp_max'] }}°C</p>
                            <p class="temp-text">🌡️ Min: {{ $day['temp_min'] }}°C</p>
                            <p class="precipitation-text">🌧️ Regen: {{ $day['precipitation'] }} mm</p>
                        </div>
                    @endforeach
                </div>
            </div>

                <livewire:frontend.climate-table.climate-table-component :locationId="$location->id" />

                <!-- Button unter der Tabelle -->
                <div class="text-start">
                    <a class="btn btn-primary" target="_blank" href="https://www.klimatabelle.de/klima/{{ $location->continent->alias }}/{{ $location->country->alias }}/klimatabelle-{{ $location->alias }}.htm">
                        @autotranslate('Mehr zu Klima & Wetter', app()->getLocale())
                    </a>
                </div>
            </div>
        </div>
    </div>



</section>


<style>
/* Wetterbox: Farbverlauf & Modernes Design */
.weather-box {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
    border-radius: 12px 0 0 12px; /* Nur links abgerundet */
    padding: 25px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    text-align: center;
}

.weather-forecast {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        justify-content: space-between;
    }

    .weather-forecast-day {
        background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        flex: 1 1 calc(25% - 1.5rem);
        min-width: 200px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .weather-forecast-day:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .weather-forecast-day p {
        margin: 0.5rem 0;
        font-size: 1rem;
        color: #333;
    }

    .weather-forecast-day p.fw-bold {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .weather-forecast-day p.fs-2 {
        font-size: 2rem;
        margin: 1rem 0;
    }

    .weather-description {
        font-size: 1.1rem;
        font-weight: 500;
        color: #34495e;
    }

    .temp-text {
        font-size: 1rem;
        color: #e74c3c;
    }

    .precipitation-text {
        font-size: 1rem;
        color: #3498db;
    }
</style>
