<div class="row">
    <div class="form-group col text-center align-content-center">
        <div class="my-1">
            <i title="Tagestemperatur" class="fas fa-thermometer-half fa-2x"></i>
            <br>
            <label>Wassertemperatur</label>
        </div>
        <div>
            <select class="form-select form-select-icon-light form-control bg-primary mb-3" name="daily_temp_start">
                <option value="">Beliebig</option>
                @foreach(config('custom.details_search_options.water_temp') as $value)
                    <option value="{{$value}}">min {{$value}} °C</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
