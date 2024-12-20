<div class="card bg-light mb-3">
    <div class="card-header">Generelle Informationen</div>
    <div class="card-body">
        <div class="row">
            {{--Monat--}}
            <div class="col-3">
                <div class="row">
                    <div class="form-group col">
                        <div class="d-flex my-2 mx-2">
                            <i class="fas fa-calendar-alt fa-2x me-3" title="Monat"></i><label>Monat</label>
                        </div>
                        <select name="month" class="details_search_result_count form-select form-select-icon-light form-control bg-primary mb-3">
                            @foreach(config('custom.months') as $key=>$month)
                                <option value="{{$key}}" @selected( $key == request()->month || ($key == 6 && !request()->month) )>{{$month}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{--Preis pro Person--}}
            <div class="col-3">
                <div class="row align-middle">
                    <div class="form-group col">
                        <div class="d-flex my-2 mx-2">
                            <i class="fas fa-suitcase-rolling fa-2x me-3" title="Preis"></i><span>Preis pro Person</span>
                        </div>
                        <select name="range_flight" class="details_search_result_count form-select form-select-icon-light form-control bg-primary mb-3">
                            <option value=" ">Beliebig</option>
                            @foreach(\App\Models\Range::where('Type', 'Flight')->orderBy('sort')->get() as $range)
                                <option value="{{ $range->id }}" @selected($range->id == request()->range_flight)>{{ $range->Range_to_show }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{--Country--}}
            <div class="col-3">
                <div class="row align-middle">
                    <div class="form-group col">
                        <div class="d-flex my-2 mx-2">
{{--                            <i class="fas fa-globe"></i>--}}
                            <i class="fas fa-globe fa-2x me-3" title="Preis"></i><span>Land</span>
                        </div>
                        <select name="country" class="details_search_result_count form-select form-select-icon-light form-control bg-primary mb-3">
                            <option value=" ">Beliebig</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" @selected($country->id == request()->country)>{{ $country->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{--Klimazone--}}
            <div class="col-3">
                <div class="row align-middle">
                    <div class="form-group col">
                        <div class="d-flex my-2 mx-2">
                            {{--                            <i class="fas fa-globe"></i>--}}
                            <i class="fas fa-cloud-sun fa-2x me-3" title="Klimazone"></i><span>Klimazone</span>
                        </div>
                        <select name="climate_zone" class="form-select form-select-icon-light form-control bg-primary mb-3 details_search_result_count" >
                            <option value="" selected>Beliebig</option>
                            @foreach($climate_lnam as $key=>$value )
                                <option value="{{$key}}" {{ $key == request()->climate_zone ? " selected" :""  }}>{{$value}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

    </div>

    </div>
</div>
