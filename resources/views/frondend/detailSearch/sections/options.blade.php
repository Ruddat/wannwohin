<div class="card bg-light mb-3">
    <div class="card-header">Details</div>
    <div class="card-body">
        <div class="row">
            {{-- Währung --}}
            <div class="col-12 col-md-6 col-lg-3">
                <label for="currency" class="d-flex align-items-center">
                    <i class="fas fa-coins fa-lg me-2 text-primary"></i>
                    <span>Währung</span>
                </label>
                <select name="currency" class="form-select bg-primary mb-3">
                    <option value="" selected>Beliebig</option>
                    @foreach($countries as $currency)
                        <option value="{{ $currency->currency_code }}"
                                @selected(request()->currency === $currency->currency_code)>
                            {{ $currency->currency_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Sprache --}}
            <div class="col-12 col-md-6 col-lg-3">
                <label for="language" class="d-flex align-items-center">
                    <i class="fas fa-language fa-lg me-2 text-primary"></i>
                    <span>Sprache</span>
                </label>
                <select name="language" class="form-select bg-primary mb-3">
                    <option value="" selected>Beliebig</option>
                    @foreach($languages as $language)
                        <option value="{{ $language->code }}"
                                @selected(request()->language === $language->code)>
                            {{ $language->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Visum --}}
            <div class="col-12 col-md-6 col-lg-3">
                <label for="visum" class="d-flex align-items-center">
                    <i class="fas fa-hand-paper fa-lg me-2 text-primary"></i>
                    <span>Visum benötigt</span>
                </label>
                <select name="visum" class="form-select bg-primary mb-3">
                    <option value="" selected>Beliebig</option>
                    <option value="yes" @selected(request()->visum === "yes")>Ja</option>
                    <option value="no" @selected(request()->visum === "no")>Nein</option>
                </select>
            </div>

            {{-- Preistendenz --}}
            <div class="col-12 col-md-6 col-lg-3">
                <label for="price_tendency" class="d-flex align-items-center">
                    <i class="fas fa-money-bill-alt fa-lg me-2 text-primary"></i>
                    <span>Preistendenz</span>
                </label>
                <select name="price_tendency" class="form-select bg-primary mb-3">
                    <option value="" selected>Beliebig</option>
                    @foreach($ranges as $range)
                        <option value="{{ $range->id }}"
                                @selected(request()->price_tendency === $range->id)>
                            {{ $range->Range_to_show }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
