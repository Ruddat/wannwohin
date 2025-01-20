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
        @foreach($currencies as $currency)
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
                        <option value="{{ $language['code'] }}"
                                @selected(request()->language === $language['code'])>
                            {{ $language['name'] }}
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

                @if(isset($price_tendencies) && $price_tendencies->isNotEmpty())
                <select name="price_tendency" class="form-select bg-primary mb-3">
                    <option value="" selected>Beliebig</option>
                    @foreach($price_tendencies as $tendency)
                        <option value="{{ $tendency }}">{{ ucfirst($tendency) }}</option>
                    @endforeach
                </select>
            @else
                <p>Keine Preistendenzen verfügbar</p>
            @endif
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('detailSearchForm');
    const filterInputs = document.querySelectorAll('.form-select');

    filterInputs.forEach(input => {
        input.addEventListener('change', () => {
            const formData = new FormData(form);
            const queryString = new URLSearchParams(formData).toString();

            fetch(`${form.action}?${queryString}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(response => response.json())
            .then(data => {
                const locationCount = document.getElementById('locationCount');
                if (locationCount) {
                    locationCount.textContent = data.count;
                }
            })
            .catch(error => {
                console.error('Fehler beim Abrufen der Daten:', error);
            });
        });
    });
});

</script>
