<div class="card bg-light mb-3">
    <div class="card-header">Urlaub</div>
    <div class="card-body">
        <div class="row">
            @foreach($activities as $key=>$activity)
                <div class="col-3">
                    <div class="row">
                        <div class="form-group col">
                            <div class="form-check d-flex">
{{--                                <input name="activities[]" {{ in_array($key, $activities) ? 'checked' : '' }} class="form-check-input" type="checkbox" value="{{$key}}"  id="tabContent9Checkbox" data-msg-required="">--}}
{{--                                <input name="continents[{{$continent->id}}]" data-old-value="@if(isset(request()->continents[$continent->id])) {{request()->continents[$continent->id]}} @endif" value="{{$continent->id}}" @if( isset(request()->continents[$continent->id]) && request()->continents[$continent->id] == $continent->id ) checked="checked" @endif" class="form-check-input" type="checkbox">--}}
                                <input name="activities[{{$key}}]" data-old-value="@if(isset(request()->activities[$key])) {{request()->activities[$key]}} @endif" value="{{$key}}" @if( isset(request()->activities[$key]) && request()->activities[$key] == $key ) checked="checked" @endif class="form-check-input details_search_result_count" type="checkbox">
                                <label class="form-check-label ms-3 me-3" for="tabContent9Checkbox">
                                    {{ $activity['title'] }}
                                </label>
                            @if($key =="list_island")
                                    <img style="margin-top: -3px;height: 30px;" src="{{asset('img/insel-icon.png')}}" als="Insel" title="Insel"/>
                            @else
                                <i class="fas {{$activity['icon']}} fa-2x  " title="Urlaub"></i>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
