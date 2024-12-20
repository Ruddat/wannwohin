<div class="card bg-light mb-3">
    <div class="card-header">Kontinent</div>
    <div class="card-body">
        <div class="row">
            @foreach($continents as $continent)
                <div class="col-3">
                    <div class="row">
                        <div class="form-group col">
                            <div class="form-check">
                                    <input name="continents[{{$continent->id}}]" data-old-value="@if(isset(request()->continents[$continent->id])) {{request()->continents[$continent->id]}} @endif" value="{{$continent->id}}" @if( isset(request()->continents[$continent->id]) && request()->continents[$continent->id] == $continent->id ) checked="checked" @endif" class="details_search_result_count form-check-input" type="checkbox">
                                <img src="{{ asset("img/location_main_img/".$continent->alias.".png") }}">
                                <label class="form-check-label" for="tabContent9Checkbox">
                                    {{ $continent->title }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
