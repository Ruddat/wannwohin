<section class="timeline-box right custom-box-shadow-2 box-shadow-2 py-4">
    <div class="container" style="background-color: #eaeff5; border-radius: 8px; padding: 20px;">
        <div class="row">
            <!-- Wetterinformationen -->
            <div class="experience-info col-lg-3 col-sm-5 bg-info p-3 rounded-start">
                <div class="text-center align-middle mb-4 pt-4">
                    <h4 class="text-color-white">
                        <i title="Wetter" class="fas fa-sun pe-1"></i> @autotranslate('Wetter', app()->getLocale()) {{ $location->title }}
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
            @autotranslate($weather_data['description'], app()->getLocale())
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
