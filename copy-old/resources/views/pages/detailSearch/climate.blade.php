@section('css')
    <style>
        #range-slider-2 .range-slider__thumb[data-lower] {
            width: 0;
        }

        #range-slider-2 .range-slider__range {
            border-radius: 6px;
        }
        #range-slider-3 {
            margin: auto;
            height: 200px;
        }
        #range-slider-4 {
            background: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23555' fill-rule='evenodd'%3E%3Cpath d='M0 40L40 0H20L0 20M40 40V20L20 40'/%3E%3C/g%3E%3C/svg%3E") #333;
        }

        #range-slider-4.range-slider__range {
            background: #ffbf00;
            transition: height .3s;
        }

        #range-slider-4 .range-slider__thumb {
            background: #faa307;
            transition: transform .3s;
        }

        #range-slider-4 .range-slider__thumb[data-active] {
            transform: translate(-50%, -50%) scale(1.25);
        }

        #range-slider-4 .range-slider__range[data-active] {
            height: 16px;
        }
        #range-slider-5 {
            height: 16px;
            background: #4b4d61;
        }

        #range-slider-5 .range-slider__range {
            background: #ff4141;
        }

        #range-slider-5 .range-slider__thumb {
            width: 30px;
            height: 30px;
            border-radius: 4px;
        }

        #range-slider-5 .range-slider__thumb[data-lower] {
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='' width='30' height='30' viewBox='0 0 24 24'%3E%3Cpath d='M3,5A2,2 0 0,1 5,3H19A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5C3.89,21 3,20.1 3,19V5M11,7A2,2 0 0,0 9,9V17H11V13H13V17H15V9A2,2 0 0,0 13,7H11M11,9H13V11H11V9Z' /%3E%3C/svg%3E") #ff4141;
        }

        #range-slider-5 .range-slider__thumb[data-upper] {
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 24 24'%3E%3Cpath d='M5,3H19A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5A2,2 0 0,1 3,19V5A2,2 0 0,1 5,3M15,10.5V9A2,2 0 0,0 13,7H9V17H13A2,2 0 0,0 15,15V13.5C15,12.7 14.3,12 13.5,12C14.3,12 15,11.3 15,10.5M13,15H11V13H13V15M13,11H11V9H13V11Z' /%3E%3C/svg%3E") #ff4141;
        }

        #range-slider-5 .range-slider__thumb[data-lower][data-active] {
            animation: rotate-anti-clockwise .9s infinite;
        }

        #range-slider-5 .range-slider__thumb[data-upper][data-active] {
            animation: rotate-clockwise .9s infinite;
        }

        @keyframes rotate-clockwise {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        @keyframes rotate-anti-clockwise {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(-360deg);
            }
        }

        #range-slider-6 {
            height: 60px;
            background: #333;
            overflow: hidden;
        }

        #range-slider-6 .range-slider__thumb {
            width: 18px;
            height: 38px;
            border-radius: 4px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' fill='%23333' viewBox='0 0 24 24'%3E%3Cpath d='M12,16A2,2 0 0,1 14,18A2,2 0 0,1 12,20A2,2 0 0,1 10,18A2,2 0 0,1 12,16M12,10A2,2 0 0,1 14,12A2,2 0 0,1 12,14A2,2 0 0,1 10,12A2,2 0 0,1 12,10M12,4A2,2 0 0,1 14,6A2,2 0 0,1 12,8A2,2 0 0,1 10,6A2,2 0 0,1 12,4Z' /%3E%3C/svg%3E") #fff;
            background-repeat: no-repeat;
            background-position: center;
        }

        #range-slider-6 .range-slider__range {
            border-radius: 6px;
            background: transparent;
            border: 4px solid #fff;
            box-sizing: border-box;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, .75);
        }

        #range-slider-7 {
            height: 24px;
            border-radius: 12px;
            background: #353644;
        }

        #range-slider-7 .range-slider__thumb {
            border: 6px solid #fff;
            box-sizing: border-box;
        }

        #range-slider-7 .range-slider__thumb[data-lower] {
            background: #0073e6;
        }

        #range-slider-7 .range-slider__thumb[data-upper] {
            background: #ee2c2c;
        }

        #range-slider-7 .range-slider__range {
            background: linear-gradient(to right, #0073e6, #ee2c2c);
            background-size: 200% 100%;
            background-position: 50% 0;
        }

        #range-slider-7 .range-slider__range[data-active],
        #range-slider-7 .range-slider__thumb[data-active]~.range-slider__range {
            animation: move-bg .75s infinite linear;
        }

        @keyframes move-bg {
            0% {
                background-position: 50% 0;
            }

            25% {
                background-position: 100% 0;
            }

            50% {
                background-position: 50% 0;
            }

            75% {
                background-position: 0% 0;
            }

            100% {
                background-position: 50% 0;
            }
        }


        /*style input*/


        .field input {
            width: 100%;
            height: 100%;
            outline: none;
            font-size: 12px;
            margin-left: 5px;
            border-radius: 2px;
            text-align: center;
            border: 1px solid #999;
            -moz-appearance: textfield;
        }
        .price-input .field {
            display: flex;
            width: 100%;
            height: 28px;
            align-items: center;
        }
        .price-input {
            width: 100%;
            display: flex;
            margin: 15px 0 20px;
        }
        /*style input*/
    </style>
    <link rel="stylesheet" href="{{ asset('assets/css/custom-range-slider-input.css') }}">
@endsection
<script src="{{ asset('assets/js/custom-range-slider-input.js') }}"></script>
@section('js')

    <script>
        {{--rangeSlider(document.querySelector('#{{$name}}_range_slider'), {--}}
        {{--    value: [{{config('custom.details_search_options.'.$name.'.min')}}, {{config('custom.details_search_options.'.$name.'.max')}}],--}}
        {{--    min:{{config('custom.details_search_options.'.$name.'.min')}},--}}
        {{--    max: {{config('custom.details_search_options.'.$name.'.max')}},--}}
        {{--    step: {{config('custom.details_search_options.'.$name.'.step')}},--}}
        {{--    thumbsDisabled: [false, false],--}}
        {{--})--}}

        // rangeSlider(document.querySelector('#range-slider-3'), {
        //     orientation: 'vertical'
        // })
        // rangeSlider(document.querySelector('#range-slider-4'))
        // rangeSlider(document.querySelector('#range-slider-5'),{
        //     step: 'any'
        // })
        // rangeSlider(document.querySelector('#range-slider-6'),{
        //     step: 'any'
        // })
        // rangeSlider(document.querySelector('#range-slider-7'),{
        //     step: 'any'
        // })

        $('#details_search_climate .set_input_field').on('mouseup  mouseleave touchmove touchend', function () {
            let slider_name = $(this).attr('data-slider-name');
            let selector_id =  $(this).attr('id');
            let min = $('#' + selector_id +" div[data-lower]").attr('aria-valuenow');
            $( '#' + slider_name + '_range_section .input-min' ).val(min);
            let max = $('#' + selector_id +" div[data-upper]").attr('aria-valuenow');
            $( '#' + slider_name + '_range_section .input-max' ).val(max);
        })
    </script>
@endsection

<div class="card bg-light mb-3">
    <div class="card-header">Klima</div>
    <div id="details_search_climate" class="card-body">
        <div class="row">
            {{--            Tagestemperatur--}}
            <div class="col-4">
{{--                <div class="row set_current_slider" id="daily_temp_range_section">--}}
{{--                    <div class="form-group col">--}}
{{--                        <div class="d-flex my-2 mx-2">--}}
{{--                            <i class="fas fa-cloud-sun fa-2x me-3" title="Tagestemperatur"></i><label>Tagestemperatur</label>--}}
{{--                        </div>--}}
{{--                        <div class="price-input">--}}
{{--                            <div class="field">--}}
{{--                                <span>Min</span>--}}
{{--                                <input type="text" class="input-min" value="0" min="0" max="35" disabled>--}}
{{--                            </div>--}}
{{--                            <div class="separator">-</div>--}}
{{--                            <div class="field">--}}
{{--                                <span>Max</span>--}}
{{--                                <input type="text" class="input-max" value="35" max="35" disabled>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="set_input_field" id="daily_temp_range_slider" data-slider-name="daily_temp"></div>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <x-search-details.climate name="daily_temp"/>
            </div>

            {{--Nachttemperatur--}}
            <div class="col-4">
              <x-search-details.climate name="night_temp"/>
            </div>

            {{--Wassertemperatur--}}
            <div class="col-4">
              <x-search-details.climate name="water_temp"/>
            </div>

            {{--Sonnenstunden--}}
            <div class="col-4">
              <x-search-details.climate name="sunshine_per_day"/>
            </div>

            {{--Regentage--}}
            <div class="col-4">
              <x-search-details.climate name="rainy_days"/>
            </div>

            {{--Luftfeuchtigkeit--}}
            <div class="col-4">
              <x-search-details.climate name="humidity"/>
            </div>


        </div>
    </div>
</div>

@stack('js')
