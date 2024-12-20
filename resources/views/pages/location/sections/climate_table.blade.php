<h4 class="text-color-dark font-weight-semibold">Klimatabelle {{ $location->title }}</h4>

<p class="text-black">

<table class="table table-striped table-bordered table-hover table-condensed location-climate-table climate-table">
    <thead>
    <tr>
{{--        <th class="center hidden-mobile hidden-sm hidden-xs hidden-md">Monat</th> --}}
        <th class="center hidden-lg"><i alt="Monat" title="Monat" class="far fa-calendar-alt"></i></th>
{{--        <th class="center hidden-mobile hidden-sm hidden-xs hidden-md">Tagestemperatur</th>--}}
        <th class="center hidden-lg"><i class="fas fa-cloud-sun"></i></th>
{{--        <th class="center hidden-mobile hidden-sm hidden-xs hidden-md">Nachttemperatur</th>--}}
        <th class="center hidden-lg"><i class="fas fa-cloud-moon"></i></th>
        @if ($climates[0]->water_temperature_avg > 1)
{{--            <th class="center hidden-mobile hidden-sm hidden-xs hidden-md">WT</th>--}}
            <th class="center hidden-lg"><i title="Wassertemperatur" class="fas fa-water"></i></th>
        @endif
{{--        <th class="center hidden-mobile hidden-sm hidden-xs hidden-md">Luftfeuchtigkeit</th>--}}
{{--        <th class="center hidden-lg"><img width="17"  src="{{ asset('img/humidity-icon.png') }}"></th>--}}
        <th class="center hidden-lg"><i title="Luftfeuchtigkeit" class="fas fa-tint"></i></th>
{{--        <th class="center hidden-mobile hidden-sm hidden-xs hidden-md">Sonnenstunden</th>--}}
        <th class="center hidden-lg"><i class="fas fa-sun"></i></th>
{{--        <th class="center hidden-mobile hidden-sm hidden-xs hidden-md">Regentage</th>--}}
        <th class="center hidden-lg"><i class="fas fa-umbrella"></i></th>
    </tr>
    </thead>
    <tbody>
    @foreach($climates as $climate)
        <tr>
            <td class="center">
                {{ $climate->month }}
            </td>
            <td class="center">
                {{ number_format($climate->daily_temperature,1, ",", ",") }} °C {{--<span class="hidden-mobile hidden-sm hidden-xs hidden-md"> °C</span>--}}
            </td>
            <td class="center">
                {{ number_format($climate->night_temperature,1, ",", ",") }} °C
            </td>
            @if ($climates[0]->water_temperature_avg > 1)
                <td class="center hidden-mobile hidden-sm hidden-xs hidden-md">
                    {{ number_format($climate->water_temperature,1, ",", ",") }}  °C
                </td>
            @endif
            <td class="center">
                {{ $climate->humidity ? number_format($climate->humidity, 1, ",", ",")." %" : '-' }}
            </td>
            <td class="center">{{ number_format($climate->sunshine_per_day,1, ",", ",") }}  h</td>
            <td class="center">{{ number_format($climate->rainy_days,1, ",", ",") }} t</td>
        </tr>
    @endforeach
    </tbody>
</table>


</p>

<div class="d-flex">
{{--    <button class="ms-auto btn btn-primary" data-bs-toggle="modal" data-bs-target="#google_map_modal">--}}
{{--        Mehr zu kimla & Wetter--}}
{{--    </button>--}}
    <a class="ms-auto btn btn-primary" target="_blank" href="https://www.klimatabelle.de/klima/{{ $location->continent->alias }}/{{ $location->country->alias }}/klimatabelle-{{ $location->alias }}.htm">Mehr zu Klima & Wetter</a>
</div>
