
<section class="timeline-box right custom-box-shadow-2 box-shadow-2 py-4">
    <div class="container" style="background-color: #eaeff5; border-radius: 8px; padding: 20px;">
        <div class="row">
<!-- Wetterinformationen -->
<div class="experience-info col-lg-3 col-sm-5 bg-info p-3 rounded-start weather-box">
    <!-- Oberer Bereich: Größer -->
    <div class="text-center align-middle mb-4 pt-3">
        <h3 class="text-color-white fw-bold">Wetter {{ $location->title }}</h3>
    </div>

    <div class="text-center align-middle mb-4">
        <h1 class="text-color-white display-5">
            <img src="{{ \App\Helpers\WeatherHelper::getWeatherIcon($weather_data['icon'] ?? null) }}"
                 alt="Wetter-Icon" class="pe-2" style="width: 64px; vertical-align: middle;">
            {{ \App\Helpers\WeatherHelper::formatTemperature($weather_data['temperature'] ?? null) }}
        </h1>
    </div>

    <div class="text-center align-middle mb-4">
        <h4 class="text-color-white">
            {{ $weather_data['description'] ?? '@autotranslate("Nicht verfügbar", app()->getLocale())' }}
        </h4>
    </div>

    <!-- Unterer Bereich: Minimalistisch -->
    <div class="text-center align-middle mb-2">
        <small class="text-color-white">
            <span class="me-1">@autotranslate('Luftfeuchtigkeit:', app()->getLocale())</span>
            {{ $weather_data['humidity'] ?? 'N/A' }}%
        </small>
    </div>
    <div class="text-center align-middle mb-3">
        <small class="text-color-white">
            <span class="me-1">@autotranslate('Wind:', app()->getLocale())</span>
            {{ \App\Helpers\WeatherHelper::formatWindSpeed($weather_data['wind_speed'] ?? null) }}
        </small>
    </div>


<!-- Minimalistische 7-Tage-Wettervorhersage (vertikal) -->
<div class="weather-forecast mt-2">
    <p class="text-color-white mb-1" style="font-size: 1.2rem; text-align: center;">7 Tage Vorhersage</p>
    @foreach ($forecast as $day)
        <div class="weather-day" title="{{ $day['weather'] }} - {{ $day['precipitation'] }} mm">
            <span>{{ substr($day['date'], 0, 5) }}</span>
            <span class="weather-icon">{{ $day['icon'] }}</span>
            <span>{{ $day['temp_max'] }}°/{{ $day['temp_min'] }}°</span>
        </div>
    @endforeach
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
    .weather-box {
        overflow: hidden;
    }

    .weather-forecast {
        font-size: 0.7rem;
    }

    .weather-day {
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 0.2rem 0;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
        margin-bottom: 0.2rem;
    }

    .weather-icon {
        display: inline-block;
        width: 16px;
        text-align: center;
        vertical-align: middle;
        margin: 0 0.2rem;
    }

    .text-color-white {
        color: #fff;
    }

    .display-5 {
        font-size: 2.5rem; /* Größere Temperatur */
    }

    @media (max-width: 576px) {
        .display-5 {
            font-size: 2rem;
        }
        .weather-forecast {
            font-size: 0.65rem;
        }
        .weather-icon {
            width: 14px;
        }
    }
    </style>
