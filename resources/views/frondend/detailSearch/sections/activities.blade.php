<div class="card bg-light mb-3 activities-filter">
    <div class="card-header">Urlaub</div>
    <div class="card-body">
        <div class="row activities-list">
            @foreach($activities as $key => $activity)
                <div class="col-3 activity-item">
                    <div class="form-group">
                        <div class="form-check align-items-center">
                            <input
                                name="activities[{{ $key }}]"
                                id="activity-{{ $key }}"
                                value="{{ $key }}"
                                class="form-check-input activity-checkbox"
                                type="checkbox"
                                @if(isset(request()->activities[$key]) && request()->activities[$key] == $key) checked @endif
                            >
                            <label class="form-check-label d-flex align-items-center ms-3" for="activity-{{ $key }}">
                                <span class="activity-title me-2">{{ $activity['title'] }}</span>
                                @if($key == "list_island")
                                    <img src="{{ asset('img/insel-icon.png') }}" alt="Insel" title="Insel" class="activity-icon">
                                @else
                                    <i class="fas {{ $activity['icon'] }} fa-2x activity-icon" title="{{ $activity['title'] }}"></i>
                                @endif
                            </label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('detailSearchForm'); // Formular-ID
    const activityCheckboxes = document.querySelectorAll('.activity-checkbox'); // Alle Aktivitäts-Checkboxen
    const locationCount = document.getElementById('locationCount'); // Element für die Anzeige des Zählers

    activityCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const formData = new FormData(form); // Formulardaten sammeln
            const queryString = new URLSearchParams(formData).toString(); // Query-String erzeugen

            fetch(`${form.action}?${queryString}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (locationCount) {
                    locationCount.textContent = data.count; // Zähler aktualisieren
                } else {
                    console.warn('Element mit der ID "locationCount" wurde nicht gefunden.');
                }
            })
            .catch(error => {
                console.error('Fehler beim Abrufen der Daten:', error);
            });
        });
    });
});

</script>

<style>




</style>
