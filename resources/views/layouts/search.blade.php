<div id="sidebar" class="sidebar-left-nav bg-black text-color-white p-4">
    <h4 class="text-center text-color-white">Suche anpassen</h4>
    <form id="quick_search_form" action="{{ route('search') }}" method="get">
        {{--        @csrf--}}
        <div class="form-group">
            <label for="continent">Kontinent</label>
            <select class="form-select py-1 quick_search_result_count" id="continent" name="continent">
                <option value="">Beliebig</option>
                @foreach(\App\Models\WwdeContinent::select('id','title')->get() as $continent)
                    <option value="{{ $continent->id }}" {{ $continent->id == request()->continent ? " selected" :"" }}>{{ $continent->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="price">Preis pro Person bis</label>
            <select class="form-select quick_search_result_count" name="price" id="price">
                <option value="">Beliebig</option>
                @foreach(\App\Models\WwdeRange::where('Type', 'Flight')->orderBy('sort')->get() as $range)
                    <option value="{{ $range->id }}" {{ $range->id == request()->price ? " selected" :""  }}>{{ $range->Range_to_show }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="urlaub">Urlaub im</label>
            <select class="form-select py-1 quick_search_result_count urlaub_type_month" id="urlaub" name="urlaub">
                @foreach(config('custom.months') as $key=>$month)
                    <option value="{{$key}}" @selected( $key == request()->urlaub || ($key == 6 && !request()->urlaub) )>{{$month}}</option>
                @endforeach

            </select>
        </div>

        <div class="form-group">
            <label for="Sonnenstunden">Sonnenstunden</label>
            <select class="form-select py-1 quick_search_result_count" id="sonnenstunden" name="sonnenstunden">
                {{--
                         <option value="less_3" {{ "less_3" == request()->sonnenstunden ? " selected":"" }}>weniger als 3</option>
                         <option value="more_3" {{ "more_3" == request()->sonnenstunden ? " selected":"" }}>mehr als 3</option>
                         <option value="less_12" {{ "less_12" == request()->sonnenstunden ? " selected":"" }}>weniger als 12</option>
                         <option value="more_13" {{ "more_13" == request()->sonnenstunden ? " selected":""  }}>mehr als 13</option>--}}
                <option value="" selecteddd>Beliebig</option>
                @for($i=3; $i<12;$i++)
                    <option value="more_{{$i}}" @selected("more_".$i == request()->sonnenstunden)>min. {{$i}}h</option>
                @endfor
{{--                <option value="more_3" @selected("more_3" == request()->sonnenstunden)>min. 3h</option>--}}
{{--                <option value="more_4" @selected("more_3" == request()->sonnenstunden)>min. 4h</option>--}}
{{--                <option value="more_5" @selected("more_3" == request()->sonnenstunden)>min. 5h</option>--}}
{{--                <option value="more_6" @selected("more_3" == request()->sonnenstunden)>min. 6h</option>--}}
{{--                <option value="more_7" @selected("more_3" == request()->sonnenstunden)>min. 7h</option>--}}
{{--                <option value="more_8" @selected("more_3" == request()->sonnenstunden)>min. 8h</option>--}}
{{--                <option value="more_9" @selected("more_3" == request()->sonnenstunden)>min. 9h</option>--}}
{{--                <option value="more_10" @selected("more_3" == request()->sonnenstunden)>min. 10h</option>--}}
{{--                <option value="more_11" @selected("more_3" == request()->sonnenstunden)>min. 11h</option>--}}
            </select>
        </div>

        <div class="form-group">
            <label for="Sonnenstunden">Wassertemperatur</label>
            <select class="form-select py-1 quick_search_result_count" id="wassertemperatur" name="wassertemperatur">
                {{--<option value="" selected>Beliebig</option>
                <option value="less_16" {{ "less_16" == request()->wassertemperatur ? " selected":""  }}>weniger als 16 °C</option>
                <option value="more_16" {{ "more_16" == request()->wassertemperatur ? " selected":""  }}>mehr als 16 °C</option>
                --}}{{--                less-27 weniger oder gleich 27--}}{{--
                <option value="less_27" {{ "less_27" == request()->wassertemperatur ? " selected":""  }}>bis 26 °C</option>
                <option value="more_27" {{ "more_27" == request()->wassertemperatur ? " selected":""  }}>mehr als 27 °C</option>
--}}
                <option value="" selected>Beliebig</option>
                <option value="more_16" {{ "more_16" == request()->wassertemperatur ? " selected":""  }}>min 16 °C</option>
                <option value="more_17" {{ "more_17" == request()->wassertemperatur ? " selected":""  }}>min 17 °C</option>
                <option value="more_18" {{ "more_18" == request()->wassertemperatur ? " selected":""  }}>min 18 °C</option>
                <option value="more_19" {{ "more_19" == request()->wassertemperatur ? " selected":""  }}>min 19 °C</option>
                <option value="more_20" {{ "more_20" == request()->wassertemperatur ? " selected":""  }}>min 20 °C</option>
                <option value="more_21" {{ "more_21" == request()->wassertemperatur ? " selected":""  }}>min 21 °C</option>
                <option value="more_22" {{ "more_22" == request()->wassertemperatur ? " selected":""  }}>min 22 °C</option>
                <option value="more_23" {{ "more_23" == request()->wassertemperatur ? " selected":""  }}>min 23 °C</option>
                <option value="more_24" {{ "more_24" == request()->wassertemperatur ? " selected":""  }}>min 24 °C</option>
                <option value="more_25" {{ "more_25" == request()->wassertemperatur ? " selected":""  }}>min 25 °C</option>
                <option value="more_26" {{ "more_26" == request()->wassertemperatur ? " selected":""  }}>min 26 °C</option>
                <option value="more_27" {{ "more_27" == request()->wassertemperatur ? " selected":""  }}>min 27 °C</option>

            </select>
        </div>

        <h5 class="text-white">Spezielle Wünsche</h5>
        <div>
            <input id="Beliebig" {{ "" == request()->spezielle ? " checked":"" }} name="spezielle" type="radio" value="" class="form-check-input quick_search_result_count" checked>
            <label for="Beliebig">Beliebig</label>
        </div>

        <div>
            <input id="Strandurlaub" {{ "list_beach" == request()->spezielle ? " checked":"" }} name="spezielle" type="radio" class="form-check-input quick_search_result_count"
                   value="list_beach">
            <label for="Strandurlaub">Strandurlaub</label>
        </div>
        <div>
            <input id="Städtereise" {{ "list_citytravel" == request()->spezielle ? " checked":"" }} name="spezielle" type="radio" class="form-check-input quick_search_result_count"
                   value="list_citytravel">
            <label for="Städtereise">Städtereise</label>
        </div>
        <div>
            <input id="Sport" {{ "list_sports" == request()->spezielle ? " checked":"" }} name="spezielle" type="radio" class="form-check-input quick_search_result_count"
                   value="list_sports">
            <label for="Sport">Sporturlaub</label>
        </div>


        {{--        <div>
                    <input id="schwimmen" name="spezielle" type="radio" class="form-check-input" value="list_swim">
                    <label for="schwimmen">schwimmen</label>
                </div>

                <div>
                    <input id="Surfen" name="spezielle" type="radio" class="form-check-input" value="list_surfing">
                    <label for="Surfen">Surfen</label>
                </div>

                <div>
                    <input id="Surfen" name="spezielle" type="radio" class="form-check-input" value="list_surfing">
                    <label for="Surfen">Surfen</label>
                </div>

                <div>
                    <input id="Tauchen" name="spezielle" type="radio" class="form-check-input" value="list_diving">
                    <label for="Tauchen">Tauchen</label>
                </div>

                <div>
                    <input id="Wintersport" name="spezielle" type="radio" class="form-check-input" value="list_wintersport">
                    <label for="Wintersport">Wintersport</label>
                </div>

                <div>
                    <input id="gehen" name="spezielle" type="radio" class="form-check-input" value="list_walking">
                    <label for="gehen">gehen</label>
                </div>

                <div>
                    <input id="Radfahren" name="spezielle" type="radio" class="form-check-input" value="list_biking">
                    <label for="Radfahren">Radfahren</label>
                </div>

                <div>
                    <input id="Klettern" name="spezielle" type="radio" class="form-check-input" value="list_climbing">
                    <label for="Klettern">Klettern</label>
                </div>--}}
        {{--        <button class="bg-warning text-white mt-3 btn"><i class="fas fa-search me-2"></i> Suche starten ({{ isset($locations) ? $locations->total() : "" }})</button>--}}
        <button class="bg-warning text-color-black mt-3 btn py-4 px-1"><i class="fas fa-search me-2"></i><span class="refresh_quick_search_result">{{$total_locations}}</span> Ergebnisse
            anzeigen
        </button>
        <div class="mt-2">
            <a class="text-decoration-underline text-white" href="{{ route('detailSearch') }}"><i class="fas fa-arrow-circle-right text-warning rounded-circle me-1 bg-white"></i>Detailsuche</a>
        </div>
    </form>
</div>
<div id="sidebarButton" class="p-2 bg-black text-color-white sidebar-left-nav"><i id="sidebar-arrow" class="fas fa-arrow-left"></i></div>
