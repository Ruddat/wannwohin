<section class="timeline-box right custom-box-shadow-2 box-shadow-2 py-4">
    <div class="container weather-container">
        <div class="row align-items-stretch">
<!-- Wetter-Widget -->
<div class="col-lg-4 col-sm-5 weather-widget-col d-flex h-100">
    <div class="widget flex-grow-1">
        <div class="header">
            <div class="date">
                <div class="day">{{ $weather['current']['date'] ?? '-' }}</div>
                <div class="weekday">{{ $weather['current']['weekday'] ?? '-' }}</div>
            </div>
            <div class="time">{{ $weather['current']['time'] ?? '-' }}</div>
        </div>

        <div class="daily-info">
            <div class="left">
                <div class="city">{{ $location->title }}</div>

                <div class="temperature">
                    {{ $weather['current']['temperature'] ?? '-' }}°C
                </div>

                <div class="details">
                    Gefühlt: {{ $weather['forecast'][0]['real_feel'] ?? '-' }}°C<br>
                    Wind: {{ $weather['current']['wind_direction'] ?? '-' }},
                          {{ $weather['current']['wind_speed'] ?? '-' }} km/h<br>
                    Luftdruck: {{ $weather['current']['pressure'] ?? '-' }} hPa<br>
                    Luftfeuchtigkeit: {{ $weather['current']['humidity'] ?? '-' }}%
                </div>
            </div>

            <div class="right">
                <img src="{{ asset('weather-icons/' . ($weather['current']['icon'] ?? 'cloudy') . '.png') }}"
                     alt="Wetter-Symbol"
                     class="weather-icon">

                <div class="sunrise-sunset">
                    Sonnenaufgang: {{ $weather['forecast'][0]['sunrise'] ?? '-' }}<br>
                    Sonnenuntergang: {{ $weather['forecast'][0]['sunset'] ?? '-' }}
                </div>
            </div>
        </div>

        <!-- 6-Tage Vorhersage -->
        <div class="forecast">
            @foreach (array_slice($weather['forecast'] ?? [], 1, 6) as $day)
                <div class="day">
                    <div class="day-name">{{ $day['weekday'] ?? '-' }}</div>

                    <img src="{{ asset('weather-icons/' . ($day['icon'] ?? 'cloudy') . '.png') }}"
                        alt="Wetter"
                        class="weather-icon-small">

                    <div class="temperature">{{ $day['temp_max'] ?? '-' }}°C</div>
                </div>
            @endforeach
        </div>
    </div>

@php
    $warmest = $climates->sortByDesc('daily_temperature')->first();
    $coldest = $climates->sortBy('daily_temperature')->first();
    $sunny   = $climates->sortByDesc('sunshine_per_day')->first();
    $rainy   = $climates->sortByDesc('rainy_days')->first();

    function m($id) {
        return \Carbon\Carbon::create(null, $id)->locale('de')->translatedFormat('F');
    }
@endphp

<div class="climate-box mb-3 mt-3">
    <h5 class="climate-box-title">🌡️ Wetter-Highlights</h5>

    <ul class="climate-list">
        <li><strong>Wärmster Monat:</strong> {{ m($warmest->month_id) }} ({{ nf($warmest->daily_temperature) }} °C)</li>
        <li><strong>Kältester Monat:</strong> {{ m($coldest->month_id) }} ({{ nf($coldest->daily_temperature) }} °C)</li>
        <li><strong>Sonnigster Monat:</strong> {{ m($sunny->month_id) }} ({{ nf($sunny->sunshine_per_day) }} h)</li>
        <li><strong>Nassester Monat:</strong> {{ m($rainy->month_id) }} ({{ $rainy->rainy_days }} Tage)</li>
    </ul>
</div>

@php
    // Prüfen ob Winterdestination
    $coldMonths = $climates->filter(fn($c) => $c->daily_temperature < 10)->count();
    $isWinterDestination = $coldMonths >= 6;

    if ($isWinterDestination) {
        // ❄️ WINTER-REISEZEIT LOGIK
        $best = $climates->filter(function ($c) {
            return $c->daily_temperature <= 8
                && $c->rainy_days <= 12     // Winter hat oft mehr "Tage", wir erlauben mehr
                && $c->sunshine_per_day >= 1; // minimal Sonne
        });

        $type = 'Winterreisezeit';
    } else {
        // ☀️ SOMMER-REISEZEIT LOGIK
        $best = $climates->filter(function($c) {
            return $c->daily_temperature >= 20
                && $c->sunshine_per_day >= 5
                && $c->rainy_days <= 10;
        });

        $type = 'Reisezeit';
    }

    $bestMonths = $best->pluck('month_id')->map(function($m){
        return \Carbon\Carbon::create(null, $m)->locale('de')->translatedFormat('F');
    })->implode(', ');
@endphp


<div class="climate-box mb-3">
    <h5 class="climate-box-title">
        ✨ Beste {{ $type }} für {{ $location->title }}
    </h5>

    @if ($bestMonths)
        <p class="climate-text">
            Die beste {{ $type }} ist:
            <strong>{{ $bestMonths }}</strong>
        </p>
    @else
        <p class="climate-text">
            Für diese Destination kann leider keine optimale {{ $type }} berechnet werden.
        </p>
    @endif
</div>



</div>





@php
  //  dd(Carbon\Carbon::now()->locale('de')->monthName);
@endphp


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

                                <th class="center"><i class="fas fa-star text-weather" title="Reise-Index"></i></th>
                                <th class="center"><i class="fas fa-cloud text-weather" title="Regenwahrscheinlichkeit"></i></th>
                                <th class="center"><i class="fas fa-sun text-weather" title="UV-Index"></i></th>
                                <th class="center"><i class="fas fa-heart text-weather" title="Komfortscore"></i></th>
                                <th class="center"><i class="fas fa-wind text-weather" title="Windgeschwindigkeit"></i></th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($climates as $climate)
                                <tr>
                                   <td class="center">
                                    {{ \Carbon\Carbon::create(null, $climate->month_id, 1)->locale('de')->translatedFormat('F') }}
                                </td>

                                    <td class="center">{{ $climate->daily_temperature ? number_format($climate->daily_temperature, 1, ',', '.') : '-' }} °C</td>
                                    <td class="center">{{ $climate->night_temperature ? number_format($climate->night_temperature, 1, ',', '.') : '-' }} °C</td>
                                    @if ($climates->first()?->water_temperature_avg < 1)
                                        <td class="center">{{ $climate->water_temperature ? number_format($climate->water_temperature, 1, ',', '.') : '-' }} °C</td>
                                    @endif
                                    <td class="center">{{ $climate->humidity ? number_format($climate->humidity, 1, ',', '.') . ' %' : '-' }}</td>
                                    <td class="center">{{ $climate->sunshine_per_day ? number_format($climate->sunshine_per_day, 1, ',', '.') : '-' }} h</td>
                                    <td class="center">{{ $climate->rainy_days ? number_format($climate->rainy_days, 1, ',', '.') : '-' }} Tage</td>

                                <td class="center">
    <span class="badge"
          style="background: {{ $climate->travel_index >= 8 ? '#4caf50' : ($climate->travel_index >= 5 ? '#ffc107' : '#f44336') }};
                 color: white;
                 padding: 3px 8px;
                 border-radius: 6px;">
        {{ $climate->travel_index ?? '-' }}
    </span>
</td>

<td class="center">
    {{ $climate->rain_probability ? number_format($climate->rain_probability, 1, ',', '.') . ' %' : '-' }}
</td>

<td class="center">
    {{ $climate->uv_index ? number_format($climate->uv_index, 1, ',', '.') : '-' }}
</td>

<td class="center">
    {{ $climate->comfort_score ?? '-' }}
</td>

<td class="center">
    {{ $climate->wind_speed_avg ? number_format($climate->wind_speed_avg, 1, ',', '.') . ' km/h' : '-' }}
</td>


                                </tr>
                            @endforeach
                        </tbody>
                    </table>


@if($climates->count())
    <div class="alert alert-info mb-3">
        🌍 <strong>Beste Reisezeit für {{ $location->title }}:</strong>
        @php
            $best = $climates->where('travel_index', '>=', 7);
            echo $best->count()
                ? $best->pluck('month_id')->map(fn($m) =>
                    \Carbon\Carbon::create(null, $m)->locale('de')->translatedFormat('F')
                )->join(', ')
                : 'Keine optimale Reisezeit verfügbar';
        @endphp
    </div>
@endif


<!-- Chart: Klimaverlauf -->
<div style="height: 280px; position: relative;">
    <canvas id="climateChart"></canvas>
</div>

<p class="text-muted small mt-2" style="font-size: 12px;">
    Hinweis: Temperatur- und Sonnenwerte basieren auf Open-Meteo Klimadaten.
    Wassertemperaturen, Luftfeuchtigkeit, UV-Index, Wind und Reise-Index werden –
    falls keine Messwerte vorliegen – anhand eines realistischen Klimamodells berechnet.
</p>


                    <div class="text-start">
                        <a class="btn btn-primary" target="_blank" href="https://www.klimatabelle.de/klima/{{ $location->continent->alias }}/{{ $location->country->alias }}/klimatabelle-{{ $location->alias }}">
                            Mehr zu Klima & Wetter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>
document.addEventListener("DOMContentLoaded", function () {

    const ctx = document.getElementById('climateChart').getContext('2d');

    const months = @json($climates->pluck('month_id')->map(fn($m) =>
        \Carbon\Carbon::create(null, $m)->locale('de')->translatedFormat('F')
    ));

    const dayTemps = @json($climates->pluck('daily_temperature'));
    const nightTemps = @json($climates->pluck('night_temperature'));
    const waterTemps = @json($climates->pluck('water_temperature'));

    const travelIndex = @json($climates->pluck('travel_index'));
    const rainProbability = @json($climates->pluck('rain_probability'));
    const uvIndex = @json($climates->pluck('uv_index'));
    const comfortScore = @json($climates->pluck('comfort_score'));
    const windAvg = @json($climates->pluck('wind_speed_avg'));

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Tagestemperatur (°C)',
                    data: dayTemps,
                    borderColor: '#ff9800',
                    backgroundColor: 'rgba(255,152,0,0.25)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Nachttemperatur (°C)',
                    data: nightTemps,
                    borderColor: '#2196f3',
                    backgroundColor: 'rgba(33,150,243,0.25)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Wassertemperatur (°C)',
                    data: waterTemps,
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76,175,80,0.25)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                },


                {
    label: 'Reise-Index',
    data: travelIndex,
    borderColor: '#9c27b0',
    backgroundColor: 'rgba(156,39,176,0.25)',
    borderWidth: 2,
    tension: 0.3,
    fill: true,
    yAxisID: 'y1'
},
{
    label: 'UV-Index',
    data: uvIndex,
    borderColor: '#ff5722',
    backgroundColor: 'rgba(255,87,34,0.25)',
    borderWidth: 2,
    tension: 0.3,
    fill: false,
    yAxisID: 'y1'
},
{
    label: 'Komfortscore',
    data: comfortScore,
    borderColor: '#009688',
    backgroundColor: 'rgba(0,150,136,0.25)',
    borderWidth: 2,
    tension: 0.3,
    fill: true,
    yAxisID: 'y1'
}

            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    suggestedMin: -5,
                    suggestedMax: 40,
                    ticks: { callback: value => value + '°' }
                },

y1: {
    position: 'right',
    suggestedMin: 0,
    suggestedMax: 12,
    ticks: { callback: value => value }
}

            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12 }
                }
            }
        }
    });
});
</script>



<style scoped>

.climate-box {
    background: #ffffff;
    border-radius: 12px;
    padding: 18px 20px;
    border: 1px solid #dbe4ec;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.climate-box-title {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 12px;
    color: #089bbc;
}

.climate-list {
    list-style: none;
    padding-left: 0;
    margin: 0;
}

.climate-list li {
    padding: 6px 0;
    font-size: 14px;
    border-bottom: 1px solid #eef3f7;
}

.climate-list li:last-child {
    border-bottom: 0;
}

.climate-text {
    font-size: 14px;
    color: #333;
    margin: 0;
}



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
