<div class="card bg-light mb-3">
    <div class="card-header">Kontinent</div>
    <div class="card-body">
        <div class="row">
            @foreach($continents as $continent)
                <div class="col-3">
                    <div class="row">
                        <div class="form-group col">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" name="continent" id="tabContent9Checkbox" data-msg-required="">
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
