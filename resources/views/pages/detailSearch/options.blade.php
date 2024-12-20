<div class="card bg-light mb-3">
    <div class="card-header">Details</div>
    <div class="card-body">
        <div class="row">
            <div class="col-3">
                <div class="row">
                    <div class="form-group col">
                        <div class="d-flex my-2 mx-2">
                            <i class="fas fa-coins fa-2x me-3" title="Währung"></i><label>Währung</label>
                        </div>
                        <select name="currency" class="form-select form-select-icon-light form-control bg-primary mb-3 details_search_result_count" >
                            <option value="" selected>Beliebig</option>
                            @foreach($currencies as $currency)
                                <option value="{{$currency->currency_code}}" {{ $currency->currency_code == request()->currency ? " selected" :""  }}>{{$currency->currency_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            {{--Sprache--}}
            <div class="col-3">
                <div class="row align-middle">
                    <div class="form-group col">
                        <div class="d-flex my-2 mx-2">
                            <i class="fas fa-language fa-2x me-3" title="Sprache"></i><span>Sprache</span>
                        </div>
                        <select name="language" class="form-select form-select-icon-light form-control bg-primary mb-3 details_search_result_count" >
                            <option value="" selected>Beliebig</option>
                            @foreach($languages as $language)
                                <option value="{{$language}}" {{ $language == request()->language ? " selected" :""  }}>{{ucfirst($language)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{--Visum benötigt--}}
            <div class="col-3">
                <div class="row align-middle">
                    <div class="form-group col">
                        <div class="d-flex my-2 mx-2">
                            <i class="fas fa-hand-paper fa-2x me-3" title="Visum benötigt"></i><span>Visum benötigt</span>
                        </div>
                        <select name="visum" class="form-select form-select-icon-light form-control bg-primary mb-3 details_search_result_count">
                            <option value="" selected>Beliebig</option>
                            <option value="yes" @selected( "yes" === request()->visum )>Ja</option> {{-- locations.stop_over = 0--}}
                            <option value="no" @selected( "no" === request()->visum ) >Nein</option> {{-- locations.stop_over > 0--}}
                        </select>
                    </div>
                </div>
            </div>

            {{--Preisendanz--}}
            <div class="col-3">
                <div class="row align-middle">
                    <div class="form-group col">
                        <div class="d-flex my-2 mx-2">
                            <i class="fas fa-money-bill-alt fa-2x me-3" title="Preistendenz"></i><span>Preistendenz</span>
                        </div>
                        <select name="preis_tendenz" class="form-select form-select-icon-light form-control bg-primary mb-3 details_search_result_count" >
{{--                            sql abfrage ist unklar--}}
                            <option value="" selected>Beliebig</option>
                                @foreach($preistendenzs as $key=>$value )
{{--                                    <option value="{{ $range->id }}" {{ $range->id == request()->preistendenz ? " selected" :""  }}>{{ $range->Range_to_show }}</option>--}}
                                    <option value="{{$key}}" {{ $key == request()->preis_tendenz ? " selected" :""  }}>{{$value}}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
