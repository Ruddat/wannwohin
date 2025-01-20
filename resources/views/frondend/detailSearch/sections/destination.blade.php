<div class="card bg-light mb-3">
    <div class="card-header">Entfernung</div>
    <div class="card-body">
        <div class="row">
            <!-- Flugstunden -->
            <div class="col-4">
                <div class="form-group col">
                    <div class="d-flex my-2 mx-2">
                        <i class="fas fa-fighter-jet fa-2x me-3" title="Flugstunden"></i><label>Flugstunden</label>
                    </div>
                    <select name="flight_duration" class="form-select form-select-icon-light form-control bg-primary mb-3 details_search_result_count">
                        <option value="" selected>Beliebig</option>
                        @foreach($flightDuration as $key => $duration)
                            <option value="{{ $key }}" @selected(request()->flight_duration == $key)>
                                {{ $duration['title'] }} {{ $duration['unit'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Entfernung -->
            <div class="col-4">
                <div class="form-group col">
                    <div class="d-flex my-2 mx-2">
                        <i class="fas fa-arrows-alt-h fa-2x me-3" title="Entfernung zum Reiseziel"></i><span>Entfernung zum Reiseziel</span>
                    </div>
                    <select name="distance_to_destination" class="form-select form-select-icon-light form-control bg-primary mb-3 details_search_result_count">
                        <option value="" selected>Beliebig</option>
                        @foreach($Destinations as $key => $destination)
                            <option value="{{ $key }}" @selected(request()->distance_to_destination == $key)>
                                {{ $destination['title'] }} {{ $destination['unit'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Direktflug -->
            <div class="col-4">
                <div class="form-group col">
                    <div class="d-flex my-2 mx-2">
                        <i class="fas fa-luggage-cart fa-2x me-3" title="Direktflug"></i><span>Direktflug</span>
                    </div>
                    <select name="stop_over" class="form-select form-select-icon-light form-control bg-primary mb-3 details_search_result_count">
                        <option value="" selected>Beliebig</option>
                        <option value="yes" @selected("yes" === request()->stop_over)>Ja</option>
                        <option value="no" @selected("no" === request()->stop_over)>Nein</option>
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

    const filterInputs = document.querySelectorAll('.details_search_result_count');
    const locationCount = document.getElementById('locationCount');

    filterInputs.forEach(input => {
        input.addEventListener('change', () => {
            const formData = new FormData(form);

            // Debugging: Werte loggen
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
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
                    locationCount.textContent = data.count; // Aktualisiere die Location-Anzahl
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
