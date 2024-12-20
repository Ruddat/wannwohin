@section('css')
<style>
      #night_temp .selector {
          position: relative;
          padding: 20px;
          width: 400px;
          color: #7e7e7e;
      }

      #night_temp .selector ul {
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

      #night_temp .selector li {
          position: relative;
          padding: 3px 20px 3px 25px;
          cursor: pointer
      }

      #night_temp.selector li:before {
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

      #night_temp .selector li[data-selected="1"]:before {
          border: 1px solid #d7d7d7;
          background-color: #fff
      }

      #night_temp .selector li[data-selected="1"]:after {
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

      #night_temp .selector li:hover {
          color: #aaa
      }

      #night_temp .selector li .total {
          position: absolute;
          right: 0;
          color: #d7d7d7
      }

      #night_temp .selector .price-slider {
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
          #night_temp .selector .price-slider {
              padding-top:8px
          }
      }

      #night_temp .selector .price-slider:before {
          position: absolute;
          top: 50%;
          left: 0;
          margin-top: 0;
          color: #39c9a9;
          content: attr(data-currency);
          -webkit-transform: translateY(-50%);
          transform: translateY(-50%)
      }

      #night_temp .selector #slider-range {
          width: 90%;
          margin-bottom: 30px;
          border: none;
          background: #e2f7f2;
          height: 3px;
          margin-left: 8px;
          margin-right: 8px
      }

      @media (min-width: 768px) {
          #night_temp .selector #slider-range {
              width:100%
          }
      }

      #night_temp .selector .ui-slider-handle {
          border-radius: 50%;
          background-color: #39c9a9;
          border: none;
          top: -14px;
          width: 28px;
          height: 28px;
          outline: none
      }

      @media (min-width: 768px) {
          .selector .ui-slider-handle {
              top:-7px;
              width: 16px;
              height: 16px
          }
      }

      #night_temp .selector .ui-slider-range {
          background-color: #d7d7d7
      }

      #night_temp .selector .slider-price {
          position: relative;
          display: inline-block;
          padding: 5px 40px;
          width: 40%;
          background-color: #e2f7f2;
          line-height: 28px;
          text-align: center
      }

      #night_temp .selector .slider-price:before {
          position: absolute;
          top: 50%;
          left: 13px;
          margin-top: 0;
          color: #39c9a9;
          content: attr(data-currency);
          -webkit-transform: translateY(-50%);
          transform: translateY(-50%)
      }

      #night_temp .selector .show-all {
          position: relative;
          padding-left: 25px;
          color: #39c9a9;
          cursor: pointer;
          line-height: 28px
      }

      #night_temp .selector .show-all:after, .selector .show-all:before {
          content: "";
          position: absolute;
          top: 50%;
          left: 4px;
          margin-top: -1px;
          color: #39c9a9;
          width: 10px;
          border-bottom: 1px solid
      }

      #night_temp .selector .show-all:after {
          -webkit-transform: rotate(90deg);
          transform: rotate(90deg)
      }

      #night_temp .selector.open ul {
          max-height: none
      }

      #night_temp .selector.open .show-all:after {
          display: none
      }
  </style>
  @endsection
<div id="night_temp">
<div class="selector">
    <div class="price-slider">
        <div id="slider-range-night_temp" class="ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content">
            <div class="ui-slider-range ui-corner-all ui-widget-header"></div>
            <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span><span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span>
        </div>
        <span id="min-price" data-currency="€" class="slider-price">0</span> <span class="seperator">-</span> <span id="max-price" data-currency="€" data-max="3500"  class="slider-price">3500 +</span>
    </div>
</div>
</div>


@section('js')
{{--    <script src="{{ asset('assets/js/custom-range-slider.js') }}"></script>--}}
    <!-- MDB -->
    <script>
        $("#night_temp").find('#slider-range-daliy-temp').empty().slider({
            range: true,
            min: 0,
            max: 3500,
            step: 50,
            slide: function( event, ui ) {
                $( "#night_temp").find('#min-price').html(ui.values[ 0 ]);

                console.log(ui.values[0])

                suffix = '';
                if (ui.values[ 1 ] == $( "#daliy_temp #max-price").data('max') ){
                    suffix = ' +';
                }
                $( "#night_temp").find('#max-price').html(ui.values[ 1 ] + suffix);
            }
        })


    </script>
@endsection
