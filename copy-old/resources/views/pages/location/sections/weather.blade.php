<div class="text-center align-middle mb-4 pt-5">
    <h4 class="text-color-white"><i title="" class="fas fa-sun pe-1"></i>
        Wetter in {{ $location->continent->title }}</h4>
</div>


<div class="text-center align-middle mb-5">
    <h1 class="text-color-white">
        <i title="Temperatur" class="fas fa-cloud-sun pe-2"></i>
        {{$weather_from_api['current_tmp']}}
    </h1>
</div>

<div class="text-center align-middle mb-5">
    <h3 class="text-color-white">
        {{$weather_from_api['weather']}}
    </h3>
</div>

<div class="text-center align-middle">
    <h5 class="text-color-white">
        <span class="me-1">Luftfeuchtigkeit:</span>
        {{$weather_from_api['humidity']}} %
    </h5>
</div>
<div class="text-center align-middle mb-1">
    <h5 class="text-color-white">
        <span class="me-1">Bewölkung:</span>
        {{$weather_from_api['current_tmp']}}
    </h5>
</div>
<div class="text-center align-middle mb-1">
    <h5 class="text-color-white">
        <span class="me-1">Wind (S):</span>
        {{$weather_from_api['wind_speed']}} km/h
    </h5>
</div>
