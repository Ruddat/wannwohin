<div class="card bg-light mb-3">
    <div class="card-header">Preise</div>
    <div class="card-body">
        <div class="row">
            <div class="col-3">
                <div class="row">
                    <div class="form-group col">
                        <div class="d-flex my-2 mx-2">
                            <i class="fas fa-plane-departure fa-2x me-3" title="Flug"></i><label>Flug</label>
                        </div>
                        <select name="price_flight" class="form-select form-select-icon-light form-control bg-primary mb-3">
                            <option>Beliebig</option>
                        </select>
                    </div>
                </div>
            </div>
            {{--Sprache--}}
            <div class="col-3">
                <div class="row align-middle">
                    <div class="form-group col">
                        <div class="d-flex my-2 mx-2">
                            <i class="fas fa-bed fa-2x me-3" title="Hotel"></i><span>Hotel</span>
                        </div>
                        <select name='price_hotel' class="form-select form-select-icon-light form-control bg-primary mb-3">
                            <option>Beliebig</option>
                        </select>
                    </div>
                </div>
            </div>

            {{--Visum benötigt--}}
            <div class="col-3">
                <div class="row align-middle">
                    <div class="form-group col">
                        <div class="d-flex my-2 mx-2">
                            <i class="fas fas fa-car fa-2x me-3" title="Mietwagen"></i><span>Mietwagen</span>
                        </div>
                        <select name="price_mietwagen" class="form-select form-select-icon-light form-control bg-primary mb-3">
                            <option>Beliebig</option>
                        </select>
                    </div>
                </div>
            </div>

            {{--Preisendanz--}}
            <div class="col-3">
                <div class="row align-middle">
                    <div class="form-group col">
                        <div class="d-flex my-2 mx-2">
                            <i class="fas fa-suitcase-rolling fa-2x me-3" title="Pauschalreise"></i><span>Pauschalreise</span>
                        </div>
                        <select name="price_pauschalreise" class="form-select form-select-icon-light form-control bg-primary mb-3">
                            <option>Beliebig</option>
                            @foreach($preistendenzs as $range )
                                <option value="{{ $range->id }}" {{ $range->id == request()->preistendenz ? " selected" :""  }}>{{ $range->Range_to_show }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
