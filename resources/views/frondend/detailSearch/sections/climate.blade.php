<div class="card bg-light mb-3">
    <div class="card-header">Klima</div>
    <div class="card-body">
        <div class="row">
            {{-- Tagestemperatur --}}
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <label for="daily_temp_slider" class="d-flex align-items-center">
                    <i class="fas fa-thermometer-half fa-lg me-2 text-warning"></i>
                    <span>Tagestemperatur</span>
                </label>
                <div id="daily_temp_slider" class="slider mt-3"></div>
                <div class="d-flex justify-content-between mt-2 gap-2">
                    <input type="number" name="daily_temp_min" id="daily_temp_min" class="form-control flex-fill filter-input" value="10">
                    <input type="number" name="daily_temp_max" id="daily_temp_max" class="form-control flex-fill filter-input" value="30">
                </div>
            </div>

            {{-- Nachttemperatur --}}
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <label for="night_temp_slider" class="d-flex align-items-center">
                    <i class="fas fa-moon fa-lg me-2 text-primary"></i>
                    <span>Nachttemperatur</span>
                </label>
                <div id="night_temp_slider" class="slider mt-3"></div>
                <div class="d-flex justify-content-between mt-2 gap-2">
                    <input type="number" name="night_temp_min" id="night_temp_min" class="form-control flex-fill filter-input" value="5">
                    <input type="number" name="night_temp_max" id="night_temp_max" class="form-control flex-fill filter-input" value="20">
                </div>
            </div>

            {{-- Wassertemperatur --}}
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <label for="water_temp_slider" class="d-flex align-items-center">
                    <i class="fas fa-water fa-lg me-2 text-info"></i>
                    <span>Wassertemperatur</span>
                </label>
                <div id="water_temp_slider" class="slider mt-3"></div>
                <div class="d-flex justify-content-between mt-2 gap-2">
                    <input type="number" name="water_temp_min" id="water_temp_min" class="form-control flex-fill filter-input" value="10">
                    <input type="number" name="water_temp_max" id="water_temp_max" class="form-control flex-fill filter-input" value="25">
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Sonnenstunden --}}
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <label for="sunshine_slider" class="d-flex align-items-center">
                    <i class="fas fa-sun fa-lg me-2 text-warning"></i>
                    <span>Sonnenstunden</span>
                </label>
                <div id="sunshine_slider" class="slider mt-3"></div>
                <div class="d-flex justify-content-between mt-2 gap-2">
                    <input type="number" name="sunshine_min" id="sunshine_min" class="form-control flex-fill filter-input" value="4">
                    <input type="number" name="sunshine_max" id="sunshine_max" class="form-control flex-fill filter-input" value="8">
                </div>
            </div>

            {{-- Regentage --}}
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <label for="rainy_days_slider" class="d-flex align-items-center">
                    <i class="fas fa-cloud-showers-heavy fa-lg me-2 text-info"></i>
                    <span>Regentage</span>
                </label>
                <div id="rainy_days_slider" class="slider mt-3"></div>
                <div class="d-flex justify-content-between mt-2 gap-2">
                    <input type="number" name="rainy_days_min" id="rainy_days_min" class="form-control flex-fill filter-input" value="5">
                    <input type="number" name="rainy_days_max" id="rainy_days_max" class="form-control flex-fill filter-input" value="15">
                </div>
            </div>

            {{-- Luftfeuchtigkeit --}}
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <label for="humidity_slider" class="d-flex align-items-center">
                    <i class="fas fa-tint fa-lg me-2 text-primary"></i>
                    <span>Luftfeuchtigkeit</span>
                </label>
                <div id="humidity_slider" class="slider mt-3"></div>
                <div class="d-flex justify-content-between mt-2 gap-2">
                    <input type="number" name="humidity_min" id="humidity_min" class="form-control flex-fill filter-input" value="30">
                    <input type="number" name="humidity_max" id="humidity_max" class="form-control flex-fill filter-input" value="70">
                </div>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/nouislider@15.6.0/dist/nouislider.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('detailSearchForm');
    const filterInputs = document.querySelectorAll('.filter-input');
    let timeoutId;

    // Slider-Konfiguration und Initialisierung
    const slidersConfig = [
        { id: 'daily_temp', min: -15, max: 50, start: [-15, 50] },
        { id: 'night_temp', min: -15, max: 30, start: [-15, 30] },
        { id: 'water_temp', min: -5, max: 40, start: [-5, 40] },
        { id: 'sunshine', min: 0, max: 16, start: [0, 16] },
        { id: 'rainy_days', min: 0, max: 30, start: [0, 30] },
        { id: 'humidity', min: 0, max: 100, start: [0, 100] },
    ];

    slidersConfig.forEach(slider => {
        const sliderElement = document.getElementById(`${slider.id}_slider`);
        const minInput = document.getElementById(`${slider.id}_min`);
        const maxInput = document.getElementById(`${slider.id}_max`);

        noUiSlider.create(sliderElement, {
            start: slider.start,
            connect: true,
            range: {
                min: slider.min,
                max: slider.max
            },
        });

        // Update inputs when slider changes
        sliderElement.noUiSlider.on('update', function (values) {
            minInput.value = Math.round(values[0]);
            maxInput.value = Math.round(values[1]);

            // Debug: Zeige die aktualisierten Werte an
           // console.log(`Slider ${slider.id} geändert:`, { min: minInput.value, max: maxInput.value });

            // Sende die Änderungen an den Controller
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                const formData = new FormData(form);
                const queryString = new URLSearchParams(formData).toString();

                // Debug: Zeige die Formulardaten und Query-Parameter an
                //console.log('Formulardaten:', Object.fromEntries(formData));
                //console.log('Query-String:', queryString);

                fetch(`${form.action}?${queryString}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Debug: Zeige die Serverantwort an
                    //console.log('Serverantwort:', data);

                    const locationCount = document.getElementById('locationCount');
                    locationCount.textContent = data.count; // Aktualisiere die Anzeige
                })
                .catch(error => {
                    console.error('Fehler beim Abrufen der Daten:', error);
                    const locationCount = document.getElementById('locationCount');
                    locationCount.textContent = 'Fehler';
                });
            }, 300); // 300ms Verzögerung
        });

        // Update slider when inputs change
        minInput.addEventListener('change', () => {
            sliderElement.noUiSlider.set([minInput.value, maxInput.value]);
        });
        maxInput.addEventListener('change', () => {
            sliderElement.noUiSlider.set([minInput.value, maxInput.value]);
        });
    });

    // Filter-Eingaben synchronisieren und senden
    filterInputs.forEach(input => {
        input.addEventListener('change', () => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                const formData = new FormData(form);
                const queryString = new URLSearchParams(formData).toString();

                // Debug: Zeige die Formulardaten und Query-Parameter an
                console.log('Formulardaten:', Object.fromEntries(formData));
                console.log('Query-String:', queryString);

                fetch(`${form.action}?${queryString}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Debug: Zeige die Serverantwort an
                    console.log('Serverantwort:', data);

                    const locationCount = document.getElementById('locationCount');
                    locationCount.textContent = data.count; // Aktualisiere die Anzeige
                })
                .catch(error => {
                    console.error('Fehler beim Abrufen der Daten:', error);
                    const locationCount = document.getElementById('locationCount');
                    locationCount.textContent = 'Fehler';
                });
            }, 300); // 300ms Verzögerung
        });
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('detailSearchForm');
    const filterInputs = document.querySelectorAll('.filter-input');
    let timeoutId;

    filterInputs.forEach(input => {
        input.addEventListener('change', () => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                const formData = new FormData(form);
                const queryString = new URLSearchParams(formData).toString();

                console.log('Formulardaten:', Object.fromEntries(formData)); // Debug
                console.log('Query-String:', queryString); // Debug

                fetch(`${form.action}?${queryString}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Serverantwort:', data); // Debug
                    const locationCount = document.getElementById('locationCount');
                    locationCount.textContent = data.count;
                })
                .catch(error => console.error('Fehler beim Abrufen der Daten:', error));
            }, 300); // 300ms Verzögerung
        });
    });
});

</script>



<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@15.6.0/dist/nouislider.min.css">
<style>

.slider {
    height: 8px;
    background: #ddd;
    border-radius: 4px;
    margin-top: 10px;
    margin-bottom: 20px; /* Abstand zwischen den Slidern */
    padding-left: 20px; /* Abstand links */
}

.noUi-handle {
    width: 16px;
    height: 16px;
    background: #007bff;
    border-radius: 50%;
    box-shadow: 0 0 6px rgba(0, 0, 0, 0.2);
    cursor: pointer;

    /* Positionierung links */
    transform: translateX(-50%);
}

.noUi-horizontal .noUi-handle {
    width: 34px;
    height: 28px;
    right: -17px;
    top: -12px;
}

.noUi-connect {
    background: #f39c12;
}

input {
    text-align: center;
    flex: 1;
}

input[readonly] {
    background-color: #f8f9fa;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .w-45 {
        width: 100%; /* Inputs auf volle Breite bringen */
    }

    .d-flex {
        flex-wrap: wrap;
    }

    .gap-2 {
        gap: 0.5rem;
    }
}
</style>


