@section('css')
<style>
    #daliy_temp .selector {
          position: relative;
          padding: 20px;
          width: 400px;
          color: #7e7e7e;
      }

    #daliy_temp .selector ul {
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

    #daliy_temp .selector li {
          position: relative;
          padding: 3px 20px 3px 25px;
          cursor: pointer
      }

    #daliy_temp .selector li:before {
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

    #daliy_temp .selector li[data-selected="1"]:before {
          border: 1px solid #d7d7d7;
          background-color: #fff
      }

    #daliy_temp .selector li[data-selected="1"]:after {
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

    #daliy_temp .selector li:hover {
          color: #aaa
      }

    #daliy_temp .selector li .total {
          position: absolute;
          right: 0;
          color: #d7d7d7
      }

    #daliy_temp .selector .price-slider {
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
          #daliy_temp .selector .price-slider {
              padding-top:8px
          }
      }

    #daliy_temp .selector .price-slider:before {
          position: absolute;
          top: 50%;
          left: 0;
          margin-top: 0;
          color: #39c9a9;
          content: attr(data-currency);
          -webkit-transform: translateY(-50%);
          transform: translateY(-50%)
      }

    #daliy_temp .selector #slider-range-daliy-temp {
          width: 90%;
          margin-bottom: 30px;
          border: none;
          background: #e2f7f2;
          height: 3px;
          margin-left: 8px;
          margin-right: 8px
      }

      @media (min-width: 768px) {
          #daliy_temp .selector #slider-range-daliy-temp {
              width:100%
          }
      }

    #daliy_temp .selector .ui-slider-handle {
          border-radius: 50%;
          background-color: #39c9a9;
          border: none;
          top: -14px;
          width: 28px;
          height: 28px;
          outline: none
      }

      @media (min-width: 768px) {
          #daliy_temp .selector .ui-slider-handle {
              top:-7px;
              width: 16px;
              height: 16px
          }
      }

    #daliy_temp .selector .ui-slider-range {
          background-color: #d7d7d7
      }

    #daliy_temp .selector .slider-price {
          position: relative;
          display: inline-block;
          padding: 5px 40px;
          width: 40%;
          background-color: #e2f7f2;
          line-height: 28px;
          text-align: center
      }

    #daliy_temp .selector .slider-price:before {
          position: absolute;
          top: 50%;
          left: 13px;
          margin-top: 0;
          color: #39c9a9;
          content: attr(data-currency);
          -webkit-transform: translateY(-50%);
          transform: translateY(-50%)
      }

    #daliy_temp .selector .show-all {
          position: relative;
          padding-left: 25px;
          color: #39c9a9;
          cursor: pointer;
          line-height: 28px
      }

    #daliy_temp .selector .show-all:after, .selector .show-all:before {
          content: "";
          position: absolute;
          top: 50%;
          left: 4px;
          margin-top: -1px;
          color: #39c9a9;
          width: 10px;
          border-bottom: 1px solid
      }

    #daliy_temp .selector .show-all:after {
          -webkit-transform: rotate(90deg);
          transform: rotate(90deg)
      }

    #daliy_temp .selector.open ul {
          max-height: none
      }

    #daliy_temp .selector.open .show-all:after {
          display: none
      }



  </style>
  @endsection
<div id="daliy_temp">
<div class="selector">
    <h4>Sonnenstunden</h4>
    <div class="price-slider">
        <div id="slider-range-daliy-temp" class="ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content">
            <div class="ui-slider-range ui-corner-all ui-widget-header"></div>
            <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span><span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span>
        </div>
        <span id="min-price" data-currency="€" class="slider-price">0</span> <span class="seperator">-</span> <span id="max-price" data-currency="€" data-max="3500"  class="slider-price">3500 +</span>
    </div>
</div>
</div>
==================================
<div class='eigenvalue'>
    <div class="slidername">Eigenvector 1</div>
    <div id='s1' class="slider"></div>
    <div id='r1'  class="slider-result">0</div>
</div>
<div class='eigenvalue'>
    <div class="slidername">Eigenvector 2</div>
    <div id='s1' class="slider"></div>
    <div id='r1'  class="slider-result">0</div>
</div>






@section('js')
{{--    <script src="{{ asset('assets/js/custom-range-slider.js') }}"></script>--}}
    <!-- MDB -->
    <script>


        $(function(){
            $('.eigenvalue').each(function(){
                $( this).find('#s1').empty().slider({
                    animate: true,
                    range: "min",
                    value: 0,
                    min: -5,
                    max: 5,
                    step: 0.1, //this gets a live reading of the value and prints it on the page
                    slide: function( event, ui ) {
                        $( this ).find('#r1').html( ui.value );
                    },
                    change: function(event, ui) {
                    }
                });
            });
        });





        $("#daliy_temp").find('#slider-range-daliy-temp').empty().slider({
            range: true,
            min: 0,
            max: 3500,
            step: 50,
            slide: function( event, ui ) {
                $( "#daliy_temp").find('#min-price').html(ui.values[ 0 ]);

                console.log(ui.values[0])

                suffix = '';
                if (ui.values[ 1 ] == $( "#daliy_temp #max-price").data('max') ){
                    suffix = ' +';
                }
                $( "#daliy_temp").find('#max-price').html(ui.values[ 1 ] + suffix);
            }
        })


    </script>
@endsection
