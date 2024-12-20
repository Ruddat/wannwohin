{{--<div class="row">
    <div class="form-group col text-center align-content-center">
        <div class="my-1">
            <i title="{{ $title }}" class="{{ $icon }} fa-2x"></i>
            <br>
            <label>{{ $title }}</label>
        </div>
        <div>
            <select class="form-select form-select-icon-light form-control bg-primary mb-3 details_search_store_value_local details_search_store_value_local" name="{{ $selectName }}">
                <option value="">Beliebig</option>
                @foreach(config('custom.details_search_options.'.$configPath) as $value)
                    <option value="{{$value}}">{{$preWord}} {{$value}}{{$afterWord}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>--}}

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/custom-range-slider.css') }}"/>
@endsection


<div class="wrapper">
    <header>
        <h2>Price Range</h2>
        <p>Use slider or enter min and max price</p>
    </header>
    <div class="price-input">
        <div class="field">
            <span>Min</span>
            {{--            <input type="number" class="input-min" value="2500">--}}
            <input type="number" class="input-min" value="0">
        </div>
        <div class="separator">-</div>
        <div class="field">
            <span>Max</span>
            {{--            <input type="number" class="input-max" value="7500">--}}
            <input type="number" class="input-max" value="35">
        </div>
    </div>
    <div class="slider">
        <div class="progress"></div>
    </div>
    <div class="range-input">
        {{--        <input type="range" class="range-min" min="0" max="10000" value="2500" step="100">--}}
        {{--        <input type="range" class="range-max" min="0" max="10000" value="7500" step="100">--}}
        <input type="range" class="range-min" min="0" max="35" value="0" step="5">
        <input type="range" class="range-max" min="0" max="35" value="35" step="5">
    </div>
</div>

@section('js')
    <script src="{{ asset('assets/js/custom-range-slider.js') }}"></script>
@endsection
