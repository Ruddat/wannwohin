<div class="card bg-light mb-3">
    <div class="card-header">Kontinent</div>
    <div class="card-body">
        <div class="row">
            <div class="col-3">
                <div class="row">
                    <div class="form-group col">
                        <div class="d-flex my-2 mx-2">
                            <i class="fas fa-coins fa-2x me-3" title="Währung"></i><label>Währung</label>
                        </div>
                        <select class="form-select form-select-icon-light form-control bg-primary mb-3">
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
                            <i class="fas fa-language fa-2x me-3" title="Sprache"></i><span>Sprache</span>
                        </div>
                        <select class="form-select form-select-icon-light form-control bg-primary mb-3">
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
                            <i class="fas fa-hand-paper fa-2x me-3" title="Visum benötigt"></i><span>Visum benötigt</span>
                        </div>
                        <select name="visum_required" class="form-select form-select-icon-light form-control bg-primary mb-3">
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
                            <i class="fas fa-money-bill-alt fa-2x me-3" title="Preisendenz"></i><span>Preisendenz</span>
                        </div>
                        <select name="preistendenz" class="form-select form-select-icon-light form-control bg-primary mb-3">
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
