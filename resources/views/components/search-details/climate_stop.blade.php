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




{{--<div class="wrapper">
    <header>
        <h3>{{config('custom.details_search_options.'.$name.'.title')}}     <span class="huge_thin_yellow"><i title="{{config('custom.details_search_options.'.$name.'.title')}}" class="{{config('custom.details_search_options.'.$name.'.icon')}} fa-1x"></i></span></h3>
--}}{{--        <p>Use slider or enter min and max price</p>--}}{{--
    </header>
    <div class="price-input">
        <div class="field">
            <span>Min</span>
            --}}{{--            <input type="number" class="input-min" value="2500">--}}{{--
            <input type="number" class="input-min" value="{{config('custom.details_search_options.'.$name.'.min')}}">
        </div>
        <div class="separator">-</div>
        <div class="field">
            <span>Max</span>
            --}}{{--            <input type="number" class="input-max" value="7500">--}}{{--
            <input type="number" class="input-max" value="{{config('custom.details_search_options.'.$name.'.max')}}">
        </div>
    </div>
    <div class="slider">
        <div class="progress"></div>
    </div>
    <div class="range-input">
        --}}{{--        <input type="range" class="range-min" min="0" max="10000" value="2500" step="100">--}}{{--
        --}}{{--        <input type="range" class="range-max" min="0" max="10000" value="7500" step="100">--}}{{--
        <input type="range" class="range-min" min="{{config('custom.details_search_options.'.$name.'.min')}}" max="{{config('custom.details_search_options.'.$name.'.max')}}" value="{{ config('custom.details_search_options.'.$name.'.min') }}" step="{{config('custom.details_search_options.'.$name.'.step')}}">
        <input type="range" class="range-max" min="{{config('custom.details_search_options.'.$name.'.min')}}" max="{{config('custom.details_search_options.'.$name.'.max')}}" value="{{ config('custom.details_search_options.'.$name.'.max') }}" step="{{config('custom.details_search_options.'.$name.'.step')}}">
    </div>
</div>--}}
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/custom-range-slider.css') }}">
@endsection
@section('js')
    <script src="{{ asset('assets/js/custom-range-slider.js') }}"></script>
@endsection
<div class="row set_current_slider" id="details_search_climate_{{$name}}" step="{{config('custom.details_search_options.'.$name.'.step')}}">
    <div class="form-group col">
        <div class="d-flex my-2 mx-2">
            <i class="{{config('custom.details_search_options.'.$name.'.icon')}} fa-2x me-3" title="{{config('custom.details_search_options.'.$name.'.title')}}"></i><label>{{config('custom.details_search_options.'.$name.'.title')}}</label>
        </div>
        <div class="price-input">
            <div class="field">
                <span>Min</span>
{{--                            <input type="number" class="input-min" value="2500">--}}
                <input type="number" class="input-min" value="{{config('custom.details_search_options.'.$name.'.min')}}" min="{{config('custom.details_search_options.'.$name.'.min')}}" max="{{config('custom.details_search_options.'.$name.'.max') - config('custom.details_search_options.'.$name.'.step')}}">
            </div>
            <div class="separator">-</div>
            <div class="field">
                <span>Max</span>
{{--                            <input type="number" class="input-max" value="7500">--}}
                <input type="number" class="input-max" value="{{config('custom.details_search_options.'.$name.'.max')}}" max="{{config('custom.details_search_options.'.$name.'.max')}}">
            </div>
        </div>
        <div class="slider">
            <div class="progress"></div>
        </div>
        <div class="range-input">
{{--                    <input type="range" class="range-min" min="0" max="10000" value="2500" step="100">--}}
{{--                    <input type="range" class="range-max" min="0" max="10000" value="7500" step="100">--}}
            <input type="range" class="range-min" min="{{config('custom.details_search_options.'.$name.'.min')}}" max="{{config('custom.details_search_options.'.$name.'.max')}}" value="{{ config('custom.details_search_options.'.$name.'.min') }}" step="{{config('custom.details_search_options.'.$name.'.step')}}">
            <input type="range" class="range-max" min="{{config('custom.details_search_options.'.$name.'.min')}}" max="{{config('custom.details_search_options.'.$name.'.max')}}" value="{{ config('custom.details_search_options.'.$name.'.max') }}" step="{{config('custom.details_search_options.'.$name.'.step')}}">
        </div>
    </div>
</div>



{{--<div class="selector">--}}
{{--    <div class="price-slider">--}}
{{--        <div id="slider-range" class="ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content">--}}
{{--            <div class="ui-slider-range ui-corner-all ui-widget-header"></div>--}}
{{--            <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span><span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span>--}}
{{--        </div>--}}
{{--        <span id="min-price" data-currency="€" class="slider-price">0</span> <span class="seperator">-</span> <span id="max-price" data-currency="€" data-max="3500"  class="slider-price">3500 +</span>--}}
{{--    </div>--}}
{{--</div>--}}
