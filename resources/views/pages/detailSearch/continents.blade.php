<div class="card bg-light mb-3">
    <div class="card-header">Kontinent</div>
    <div class="card-body">
        <div class="row">
            @foreach($continents as $continent)
                <div class="col-6 col-md-4 col-lg-3 mb-4">
                    <div class="continent-card position-relative">
                        <!-- Checkbox Input -->
                        <input
                            name="continents[{{$continent->id}}]"
                            id="continent_{{$continent->id}}"
                            value="{{$continent->id}}"
                            class="form-check-input continent-checkbox"
                            type="checkbox"
                            @if(request()->has('continents') && in_array($continent->id, array_keys(request()->continents)))
                                checked
                            @endif
                        >
                        <!-- Hintergrundbild -->
                        <div
                            class="continent-image"
                            data-images='[
                                "{{ asset($continent->image1_path) }}",
                                "{{ asset($continent->image2_path) }}",
                                "{{ asset($continent->image3_path) }}"
                            ]'
                            style="background-image: url('{{ asset($continent->image1_path) }}')"
                        ></div>
                        <!-- Overlay mit Titel -->
                        <div class="continent-overlay">
                            <span class="continent-title">{{ $continent->title }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .continent-card {
        position: relative;
        width: 100%;
        height: 150px;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .continent-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
    }

    .continent-image {
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        filter: grayscale(100%);
        transition: filter 0.3s ease, opacity 1s ease-in-out;
        position: absolute;
        top: 0;
        left: 0;
        opacity: 1;
    }

    .continent-card:hover .continent-image {
        filter: grayscale(0%);
    }

    .continent-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.5);
        color: #fff;
        text-align: center;
        padding: 10px 0;
        font-size: 1rem;
        font-weight: bold;
        transition: background 0.3s ease;
    }

    .continent-card:hover .continent-overlay {
        background: rgba(0, 0, 0, 0.7);
    }

    .continent-checkbox {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 20px;
        height: 20px;
        z-index: 2;
    }

    .continent-card .continent-checkbox:checked + .continent-image {
        border: 10px solid #007bff;
        box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);

        }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const continentImages = document.querySelectorAll('.continent-image');

        continentImages.forEach(image => {
            const images = JSON.parse(image.dataset.images || '[]');
            if (images.length > 1) {
                let index = 0;

                setInterval(() => {
                    index = (index + 1) % images.length; // Wechsel zwischen Bildern
                    image.style.opacity = 0; // Fade-out
                    setTimeout(() => {
                        image.style.backgroundImage = `url('${images[index]}')`;
                        image.style.opacity = 1; // Fade-in
                    }, 1000); // Wartezeit für den Übergang
                }, 10000); // Alle 4 Sekunden wechseln
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('detailSearchForm');
    const continentCheckboxes = document.querySelectorAll('.continent-checkbox');
    const locationCount = document.getElementById('locationCount');

    continentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
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
