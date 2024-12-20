<div class="card bg-light mb-3">
    <div class="card-header">Entfernung</div>
    <div class="card-body">
        <div class="row">
            <div class="col-4">
                <div class="row">
                    <div class="form-group col">
                        <div class="d-flex my-2 mx-2">
                            <i class="fas fa-fighter-jet fa-2x me-3" title="Flugstunden"></i><label>Flugstunden</label>
                        </div>
                        <select name="flight_duration" class="form-select form-select-icon-light form-control bg-primary mb-3 details_search_result_count" >
                        {{--  max flight duration--}}
                            <option value="" selected>Beliebig</option>
                            @foreach($flightDuration as $key => $duration )
                                    <option value="{{$key}}" @selected( $key == request()->flight_duration )>{{$duration['title']}} {{$duration['unit']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            {{--Entfernung zum Reiseziel--}}
            <div class="col-4">
                <div class="row align-middle">
                    <div class="form-group col">
                        <div class="d-flex my-2 mx-2">
                            <i class="fas fa-arrows-alt-h fa-2x me-3" title="Entfernung zum Reiseziel"></i><span>Entfernung zum Reiseziel</span>
                        </div>
                        <select name="distance_to_destination" class="form-select form-select-icon-light form-control bg-primary mb-3 details_search_result_count">
                            <option value="" selected>Beliebig</option>
                            {{-- max destination--}}
                            @foreach($Destinations as $key => $destination )
                                <option value="{{$key}}" @selected( $key == request()->distance_to_destination ) >{{  $destination['title']  }} {{  $destination['unit']  }}</option>
                            @endforeach

                        </select>
                    </div>
                </div>
            </div>

            {{--Visum benötigt--}}
            <div class="col-4">
                <div class="row align-middle">
                    <div class="form-group col">
                        <div class="d-flex my-2 mx-2">
                            <i class="fas fa-luggage-cart fa-2x me-3" title="Direktflug"></i><span>Direktflug</span>
                        </div>
                        <select name="stop_over" class="form-select form-select-icon-light form-control bg-primary mb-3 details_search_result_count">
                            <option value="" selected>Beliebig</option>
                            <option value="yes" @selected( "yes" === request()->stop_over ) >Ja</option>    {{-- locations.stop_over = 0--}}
                            <option value="no" @selected( "no" == request()->stop_over )>Nein</option>  {{-- locations.stop_over > 0--}}
                        </select>
                    </div>
                </div>
            </div>

            {{--Preisendanz--}}

        </div>
    </div>
</div>
