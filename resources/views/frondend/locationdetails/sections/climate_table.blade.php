<section class="timeline-box right custom-box-shadow-2 box-shadow-2 py-4">
    <div class="container weather-container">
        <div class="row">

<!-- Wetter-Widget -->
<div class="col-lg-4 col-sm-5 p-3 weather-widget-col">
    <div class="widget">
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
                    Gefühlt: {{ $forecast[0]['real_feel'] }}°<br>
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
                    <img src="{{ asset('weather-icons/' . $day['icon']) }}.png" alt="Wetter-Symbol" class="weather-icon-small">
                    <div class="temperature">{{ $day['temp_max'] }}°C</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

            <!-- Klimatabelle -->
            <div class="col-lg-8 col-sm-7 bg-color-light px-3 py-3 climate-table-col">
                <h4 class="text-color-dark font-weight-semibold mb-4">Klimatabelle {{ $location->title }}</h4>
                <table class="table table-striped table-bordered table-hover table-condensed location-climate-table climate-table mb-4">
                    <thead>
                        <tr>
                            <th class="center"><i class="far fa-calendar-alt" title="@autotranslate('Monat', app()->getLocale())"></i></th>
                            <th class="center"><i class="fas fa-cloud-sun" title="@autotranslate('Tagesdurchschnittstemperatur', app()->getLocale())"></i></th>
                            <th class="center"><i class="fas fa-cloud-moon" title="@autotranslate('Nachtdurchschnittstemperatur', app()->getLocale())"></i></th>
                            @if ($climates->first()?->water_temperature_avg > 1)
                                <th class="center"><i class="fas fa-water" title="@autotranslate('Wassertemperatur', app()->getLocale())"></i></th>
                            @endif
                            <th class="center"><i class="fas fa-tint" title="@autotranslate('Luftfeuchtigkeit', app()->getLocale())"></i></th>
                            <th class="center"><i class="fas fa-sun" title="@autotranslate('Sonnenstunden', app()->getLocale())"></i></th>
                            <th class="center"><i class="fas fa-umbrella" title="@autotranslate('Regentage', app()->getLocale())"></i></th>
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
            margin-top: auto;
            color: #089bbc;
        }

    .daily-info {
        background-color: #edf2f6;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
    }

    .city {
        font-size: 20px;
        font-weight: bold;
        color: #089bbc;
    }

    .temperature {
        font-size: 24px;
        font-weight: bold;
        color: #089bbc;
    }

    .details, .sunrise-sunset {
        font-size: 14px;
        color: #089bbc;
        line-height: 1.6;
    }



    .weather-icon {
    width: 128px;
    height: auto;
    /* background-color: #ccc; */
    /* border-radius: 50%; */
    margin-bottom: 10px;
}

    .forecast .day {
        background-color: #edf2f6;
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .day-name {
        font-size: 16px;
        font-weight: bold;
        color: #089bbc;
    }

    .weather-icon-small {
    width: 40px;
    height: auto;
    /* background-color: #ccc; */
    /* border-radius: 50%; */
}

    .forecast .temperature {
        font-size: 18px;
    }

    .climate-table-col {
        border-radius: 0 15px 15px 0;
    }
</style>
