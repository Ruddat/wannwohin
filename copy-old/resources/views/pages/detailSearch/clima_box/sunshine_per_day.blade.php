@section('css')
    <style>
        #sunshine_per_day .selector {
            position: relative;
            padding: 20px;
            width: 400px;
            color: #7e7e7e;
        }

        #sunshine_per_day .selector ul {
            position: relative;
            display: block;
            overflow: auto;
            min-width: 138px;
            max-height: 200px;
            background: #fff;
            list-style: none;
            white-space: inherit;
            padding-right: 17px;
            width: calc(100% + 17px)
        }

        #sunshine_per_day .selector li {
            position: relative;
            padding: 3px 20px 3px 25px;
            cursor: pointer
        }

        #sunshine_per_day .selector li:before {
            position: absolute;
            top: 50%;
            left: 0;
            top: 4px;
            display: inline-block;
            margin-right: 9px;
            width: 17px;
            height: 17px;
            background-color: #f4f4f4;
            border: 1px solid #d5d5d5;
            content: ""
        }

        #sunshine_per_day .selector li[data-selected="1"]:before {
            border: 1px solid #d7d7d7;
            background-color: #fff
        }

        #sunshine_per_day .selector li[data-selected="1"]:after {
            position: absolute;
            top: 50%;
            left: 3px;
            top: 11px;
            display: inline-block;
            width: 4px;
            height: 10px;
            border-right: 2px solid;
            border-bottom: 2px solid;
            background: none;
            color: #39c9a9;
            content: "";
            -webkit-transform: rotate(40deg) translateY(-50%);
            transform: rotate(40deg) translateY(-50%)
        }

        #sunshine_per_day .selector li:hover {
            color: #aaa
        }

        #sunshine_per_day .selector li .total {
            position: absolute;
            right: 0;
            color: #d7d7d7
        }

        #sunshine_per_day .selector .price-slider {
            text-align: center;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            -webkit-box-pack: justify;
            -ms-flex-pack: justify;
            justify-content: space-between;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            position: relative;
            padding-top: 17px
        }

        @media (min-width: 768px) {
            #sunshine_per_day .selector .price-slider {
                padding-top:8px
            }
        }

        #sunshine_per_day .selector .price-slider:before {
            position: absolute;
            top: 50%;
            left: 0;
            margin-top: 0;
            color: #39c9a9;
            content: attr(data-currency);
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%)
        }

        #sunshine_per_day .selector #slider-range-sunshine_per_day {
            width: 90%;
            margin-bottom: 30px;
            border: none;
            background: #e2f7f2;
            height: 3px;
            margin-left: 8px;
            margin-right: 8px
        }

        @media (min-width: 768px) {
            #sunshine_per_day .selector #slider-range-sunshine_per_day {
                width:100%
            }
        }

        #sunshine_per_day .selector .ui-slider-handle {
            border-radius: 50%;
            background-color: #39c9a9;
            border: none;
            top: -14px;
            width: 28px;
            height: 28px;
            outline: none
        }

        @media (min-width: 768px) {
            #sunshine_per_day .selector .ui-slider-handle {
                top:-7px;
                width: 16px;
                height: 16px
            }
        }

        #sunshine_per_day .selector .ui-slider-range {
            background-color: #d7d7d7
        }

        #sunshine_per_day .selector .slider-price {
            position: relative;
            display: inline-block;
            padding: 5px 40px;
            width: 40%;
            background-color: #e2f7f2;
            line-height: 28px;
            text-align: center
        }

        #sunshine_per_day .selector .slider-price:before {
            position: absolute;
            top: 50%;
            left: 13px;
            margin-top: 0;
            color: #39c9a9;
            content: attr(data-currency);
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%)
        }

        #sunshine_per_day .selector .show-all {
            position: relative;
            padding-left: 25px;
            color: #39c9a9;
            cursor: pointer;
            line-height: 28px
        }

        #sunshine_per_day .selector .show-all:after, #sunshine_per_day .selector .show-all:before {
            content: "";
            position: absolute;
            top: 50%;
            left: 4px;
            margin-top: -1px;
            color: #39c9a9;
            width: 10px;
            border-bottom: 1px solid
        }

        #sunshine_per_day .selector .show-all:after {
            -webkit-transform: rotate(90deg);
            transform: rotate(90deg)
        }

        #sunshine_per_day .selector.open ul {
            max-height: none
        }

        #sunshine_per_day .selector.open .show-all:after {
            display: none
        }
    </style>
@endsection
<div id="sunshine_per_day">
<div class="selector" >
    <h4>Sonnenstunden</h4>
    <div class="price-slider">
        <div id="slider-range-sunshine_per_day" class="ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content">
            <div class="ui-slider-range ui-corner-all ui-widget-header"></div>
            <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span><span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span>
        </div>
        <span id="min-price" data-currency="€" class="slider-price">0</span>
        <span class="seperator">-</span>
        <span id="max-price" data-currency="€" data-max="3500"  class="slider-price">3500 +</span>
    </div>
</div>
</div>


@section('js')

    {{--    <script src="{{ asset('assets/js/custom-range-slider.js') }}"></script>--}}
    <!-- MDB -->
    <script>
        $("#sunshine_per_day").find('#slider-range-sunshine_per_day').empty().slider({
            range: true,
            min: 0,
            max: 5500,
            step: 50,
            slide: function( event, ui ) {
                $("#sunshine_per_day").find('#min-price').html(ui.values[ 0 ]);

                console.log(ui.values[0])

                suffix = '';
                if (ui.values[ 1 ] == $( "#sunshine_per_day #max-price-sunshine_per_day").data('max') ){
                    suffix = ' +';
                }
                $( "#sunshine_per_day").find('#max-price').html(ui.values[ 1 ] + suffix);
            }
        })
    </script>
@endsection
