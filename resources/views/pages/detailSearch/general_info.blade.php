<div class="card bg-light mb-3">
    <div class="card-header">Generelle Informationen</div>
    <div class="card-body">
        <div class="row">
            {{-- Monat --}}
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-alt fa-2x me-3"></i><label>Monat</label>
                    </div>
                    <select name="month" class="form-select bg-primary mb-3 filter-input">
                        <option value="">Beliebig</option>
                        @foreach(config('custom.months') as $key => $month)
                            <option value="{{ $key }}" @selected($key == request()->month)>{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Preis pro Person --}}
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-suitcase-rolling fa-2x me-3"></i><label>Preis pro Person</label>
                    </div>
                    <select name="range_flight" class="form-select bg-primary mb-3 filter-input">
                        <option value="">Beliebig</option>
                        @foreach($ranges as $range)
                            <option value="{{ $range->id }}" @selected($range->id == request()->range_flight)>{{ $range->Range_to_show }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Country --}}
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-globe fa-2x me-3"></i><label>Land</label>
                    </div>
                    <select name="country" class="form-select bg-primary mb-3 filter-input">
                        <option value="">Beliebig</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" @selected($country->id == request()->country)>{{ $country->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Klimazone --}}
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="form-group">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-cloud-sun fa-2x me-3"></i><label>Klimazone</label>
                    </div>
                    <select name="climate_zone" class="form-select bg-primary mb-3 filter-input">
                        <option value="">Beliebig</option>
                        @foreach($climate_lnam as $key => $value)
                            <option value="{{ $key }}" @selected($key == request()->climate_zone)>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('detailSearchForm');
    if (!form) {
        console.error('Formular mit ID "detailSearchForm" nicht gefunden.');
        return;
    }

    const filterInputs = document.querySelectorAll('.filter-input');
    const locationCount = document.getElementById('locationCount');

    filterInputs.forEach(input => {
        input.addEventListener('change', () => {
            const formData = new FormData(form);

            // Werte bereinigen
            for (let [key, value] of formData.entries()) {
                formData.set(key, value.trim());
            }

            const queryString = new URLSearchParams(formData).toString();
            console.log('Gesendete Query-Parameter:', queryString);

            fetch(`${form.action}?${queryString}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (locationCount) {
                    locationCount.textContent = data.count;
                } else {
                    console.error('Element mit ID "locationCount" nicht gefunden.');
                }
            })
            .catch(error => {
                console.error('Fehler beim Abrufen der Daten:', error);
            });
        });
    });
});


</script>
