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
                    <input type="number" id="daily_temp_min" class="form-control flex-fill" value="10">
                    <input type="number" id="daily_temp_max" class="form-control flex-fill" value="30">
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
                    <input type="number" id="night_temp_min" class="form-control flex-fill" value="5">
                    <input type="number" id="night_temp_max" class="form-control flex-fill" value="20">
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
                    <input type="number" id="water_temp_min" class="form-control flex-fill" value="10">
                    <input type="number" id="water_temp_max" class="form-control flex-fill" value="25">
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
                    <input type="number" id="sunshine_min" class="form-control flex-fill" value="4">
                    <input type="number" id="sunshine_max" class="form-control flex-fill" value="8">
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
                    <input type="number" id="rainy_days_min" class="form-control flex-fill" value="5">
                    <input type="number" id="rainy_days_max" class="form-control flex-fill" value="15">
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
                    <input type="number" id="humidity_min" class="form-control flex-fill" value="30">
                    <input type="number" id="humidity_max" class="form-control flex-fill" value="70">
                </div>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/nouislider@15.6.0/dist/nouislider.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const slidersConfig = [
        { id: 'daily_temp', min: 0, max: 50, start: [10, 30] },
        { id: 'night_temp', min: 0, max: 30, start: [5, 20] },
        { id: 'water_temp', min: 0, max: 40, start: [10, 25] },
        { id: 'sunshine', min: 0, max: 12, start: [4, 8] },
        { id: 'rainy_days', min: 0, max: 30, start: [5, 15] },
        { id: 'humidity', min: 0, max: 100, start: [30, 70] },
    ];

    slidersConfig.forEach(slider => {
        const sliderElement = document.getElementById(`${slider.id}_slider`);
        const minInput = document.getElementById(`${slider.id}_min`);
        const maxInput = document.getElementById(`${slider.id}_max`);

        // Initialize Slider without tooltips
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
        });

        // Update slider when inputs change
        const syncSliderWithInputs = () => {
            sliderElement.noUiSlider.set([minInput.value, maxInput.value]);
        };

        minInput.addEventListener('change', syncSliderWithInputs);
        maxInput.addEventListener('change', syncSliderWithInputs);
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


