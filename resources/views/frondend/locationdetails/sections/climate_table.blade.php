<section class="timeline-box right custom-box-shadow-2 box-shadow-2 py-4">
    <div class="container weather-container">
        <div class="row align-items-stretch">
            <!-- Wetter-Widget -->
            <div class="col-lg-4 col-sm-5 weather-widget-col d-flex h-100">
                <div class="widget flex-grow-1">
                    <div class="header">
                        <div class="date">
                            <div class="day">{{ $weather_data_widget['date'] }}</div>
                            <div class="weekday">{{ $weather_data_widget['weekday'] }}</div>
                        </div>
                        <div class="time">{{ $weather_data_widget['time'] }}</div>
                    </div>
                    <div class="daily-info">
                        <div class="left">
                            <div class="city">{{ $location->title }}</div>
                            <div class="temperature">{{ $weather_data_widget['temperature'] }}°C</div>
                            <div class="details">
                                Gefühlt: {{ $forecast[0]['real_feel'] }}°C<br>
                                Wind: {{ $weather_data_widget['wind_direction'] }}, {{ $weather_data_widget['wind_speed'] }} km/h<br>
                                Luftdruck: {{ $weather_data_widget['pressure'] }} hPa<br>
                                Luftfeuchtigkeit: {{ $weather_data_widget['humidity'] }}%
                            </div>
                        </div>
                        <div class="right">
                            <img src="{{ asset('weather-icons/' . $weather_data_widget['icon']) }}.png" alt="Wetter-Symbol" class="weather-icon">
                            <div class="sunrise-sunset">
                                Sonnenaufgang: {{ $forecast[0]['sunrise'] }}<br>
                                Sonnenuntergang: {{ $forecast[0]['sunset'] }}
                            </div>
                        </div>
                    </div>
                    <div class="forecast">
                        @foreach (array_slice($forecast, 1, 6) as $day)
                            <div class="day">
                                <div class="day-name">{{ $day['weekday'] }}</div>
                                <img src="{{ asset('weather-icons/' . $day['icon']) }}.png" alt="Wetter-Symbol" class="weather-icon-small" title="{{ $day['weather'] ?? 'Unbekanntes Wetter' }}">
                                <div class="temperature">{{ $day['temp_max'] }}°C</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Klimatabelle -->
            <div class="col-lg-8 col-sm-7 bg-color-light px-3 py-3 climate-table-col d-flex h-100">
                <div class="flex-grow-1">
                    <h4 class="text-color-dark font-weight-semibold mb-4">Durchschnittliche Klimawerte {{ $location->title }}</h4>
                    <table class="table table-striped table-bordered table-hover table-condensed location-climate-table climate-table mb-4">
                        <thead>
                            <tr>
                                <th class="center"><i class="far fa-calendar-alt text-weather" title="Monat"></i></th>
                                <th class="center"><i class="fas fa-cloud-sun text-weather" title="Durchschnittstemperatur tagsüber"></i></th>
                                <th class="center"><i class="fas fa-cloud-moon text-weather" title="Durchschnittstemperatur nachts"></i></th>
                                @if ($climates->first()?->water_temperature_avg < 1)
                                    <th class="center"><i class="fas fa-water text-weather" title="Wassertemperatur"></i></th>
                                @endif
                                <th class="center"><i class="fas fa-tint text-weather" title="Luftfeuchtigkeit"></i></th>
                                <th class="center"><i class="fas fa-sun text-weather" title="Sonnenstunden"></i></th>
                                <th class="center"><i class="fas fa-umbrella text-weather" title="Regentage"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($climates as $climate)
                                <tr>
                                    <td class="center">{{ \Carbon\Carbon::createFromFormat('m', $climate->month_id)->locale('de')->format('F') }}</td>
                                    <td class="center">{{ $climate->daily_temperature ? number_format($climate->daily_temperature, 1, ',', '.') : '-' }} °C</td>
                                    <td class="center">{{ $climate->night_temperature ? number_format($climate->night_temperature, 1, ',', '.') : '-' }} °C</td>
                                    @if ($climates->first()?->water_temperature_avg < 1)
                                        <td class="center">{{ $climate->water_temperature ? number_format($climate->water_temperature, 1, ',', '.') : '-' }} °C</td>
                                    @endif
                                    <td class="center">{{ $climate->humidity ? number_format($climate->humidity, 1, ',', '.') . ' %' : '-' }}</td>
                                    <td class="center">{{ $climate->sunshine_per_day ? number_format($climate->sunshine_per_day, 1, ',', '.') : '-' }} h</td>
                                    <td class="center">{{ $climate->rainy_days ? number_format($climate->rainy_days, 1, ',', '.') : '-' }} Tage</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-start">
                        <a class="btn btn-primary" target="_blank" href="https://www.klimatabelle.de/klima/{{ $location->continent->alias }}/{{ $location->country->alias }}/klimatabelle-{{ $location->alias }}.htm">
                            Mehr zu Klima & Wetter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style scoped>
/* Scoped-Style für diese Blade-Datei, um Einflüsse auf andere Templates zu vermeiden */
.weather-container {
    background-color: #eaeff5;
    border-radius: 8px;
    padding: 20px;
}

.weather-widget-col {
    border-radius: 15px 0 0 15px;
}

.widget {
    background-color: #ffffff;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    /* Damit das Widget die volle Höhe der Spalte einnimmt */
    height: 100%;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.header .date {
    text-align: left;
}

.header .date .day {
    font-size: 18px;
    font-weight: bold;
    color: #089bbc;
}

.header .date .weekday {
    font-size: 14px;
    color: #666;
}

.header .time {
    font-size: 18px;
    font-weight: bold;
    text-align: right;
    color: #089bbc;
}

.daily-info {
    background-color: #edf2f6;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.left {
    text-align: left;
}

.left .city {
    font-size: 20px;
    font-weight: bolder;
    color: #089bbc;
    margin-bottom: 5px;
}

.left .temperature {
    font-size: 28px;
    font-weight: bold;
    color: #089bbc;
    margin-bottom: 5px;
    text-align: center;
}

.left .details {
    font-size: 10px;
    color: #089bbc;
    line-height: 1.4;
    width: max-content;
}

.right {
    text-align: right;
}

.right .weather-icon {
    width: 128px;
    height: auto;
    margin-bottom: 10px;
    display: inline-block;
}

.right .sunrise-sunset {
    font-size: 10px;
    color: #089bbc;
    line-height: 1.4;
    display: inline-block;
}

.forecast .day {
    background-color: #edf2f6;
    border-radius: 10px;
    padding: 10px;
    margin-bottom: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
}

.day-name {
    font-size: 16px;
    font-weight: bold;
    color: #089bbc;
    flex: 1;
}

.weather-icon-small {
    width: 40px;
    height: auto;
    flex: 0 0 auto;
}

.forecast .temperature {
    font-size: 18px;
    color: #089bbc;
    flex: 1;
}

.weather-icon-small[src*='snowy'] {
    position: relative;
}

.weather-icon-small[src*='snowy']::after {
    content: '';
    position: absolute;
    width: 10px;
    height: 10px;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 50%;
    animation: snowFall 2s infinite;
}

@keyframes snowFall {
    0% { transform: translateY(0) rotate(0deg); opacity: 0.8; }
    100% { transform: translateY(20px) rotate(360deg); opacity: 0; }
}

.climate-table-col {
    border-radius: 15px 15px 15px 15px;
}

.text-weather {
    color: #089bbc;
}

.weather-icon, .weather-icon-small {
    transition: transform 0.3s ease;
}

.weather-icon:hover, .weather-icon-small:hover {
    transform: scale(1.1);
}

/* Spezifische Animation für sonnige oder windige Icons */
.weather-icon[src*='sunny'], .weather-icon-small[src*='sunny'],
.weather-icon[src*='sunny-waves'], .weather-icon-small[src*='sunny-waves'] {
    animation: sunShine 2s infinite ease-in-out;
}

.weather-icon[src*='partly-cloudy'], .weather-icon-small[src*='partly-cloudy'],
.weather-icon[src*='windy'], .weather-icon-small[src*='windy'] {
    animation: windBlow 2s infinite ease-in-out;
}

@keyframes sunShine {
    0%, 100% { transform: rotate(0deg) scale(1); }
    50% { transform: rotate(5deg) scale(1.05); }
}

@keyframes windBlow {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(5px); }
}

.climate-table tbody tr:nth-child(even) {
    background-color: #f0f7ff;
}

.climate-table tbody tr:nth-child(odd) {
    background-color: #e6f0ff;
}

.weather-widget-col, .climate-table-col {
    display: flex;
    flex-direction: column;
}

.widget, .climate-table-col > div {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

@media (max-width: 768px) {
    .forecast .day {
        flex-direction: column;
        text-align: center;
        padding: 8px;
    }

    .day-name, .forecast .temperature {
        font-size: 14px;
    }

    .weather-icon-small {
        margin: 5px 0;
    }

    .weather-widget-col, .climate-table-col {
        height: auto;
    }
}

@media (max-width: 768px) {
    .climate-table {
        font-size: 13px;
    }

    .climate-table td,
    .climate-table th {
        padding: 6px 4px !important;
    }

    .climate-table th i,
    .climate-table td i {
        font-size: 14px;
    }

    .climate-table td {
        white-space: nowrap;
        text-align: center;
    }

    .climate-table tbody tr {
        display: table-row;
    }

    .climate-table thead {
        display: table-header-group;
    }

    .climate-table td,
    .climate-table th {
        vertical-align: middle;
    }
}

</style>
