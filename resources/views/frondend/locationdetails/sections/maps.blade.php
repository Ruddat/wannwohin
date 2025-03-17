<div class="card-body p-4">
    <!-- OpenStreetMap Section (volle Breite oben) -->
    <div class="map-container rounded shadow-lg mb-4" id="openstreetmap"
         style="height: 200px; width: 100%;"
         data-aos="fade-down">
    </div>

    <!-- Description Section -->
    <div class="content-section" data-aos="fade-up">
        <h4 class="text-color-dark fw-bold mb-3">
            <i class="fas fa-map-marker-alt me-2"></i>
            @autotranslate("Karte & Route für {$location->title}", app()->getLocale())
        </h4>
        <p class="text-black">{!! $location->text_short !!}</p>
        <p class="text-black">In einer Flugzeit von ca. {{ $location->flight_hours }} Stunden von Frankfurt ist man am Ziel.</p>
        <div class="d-flex">
            <button class="ms-auto btn btn-primary" data-bs-toggle="modal" data-bs-target="#google_map_modal">
                Position auf der Karte
            </button>
        </div>
    </div>




</div>

<!-- OpenStreetMap Integration -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lat = {{ $location->lat }};
        const lon = {{ $location->lon }};

        const map = L.map('openstreetmap').setView([lat, lon], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        L.marker([lat, lon]).addTo(map)
            .bindPopup('{{ $location->title }}')
            .openPopup();
    });
</script>

<!-- Zusätzliches CSS -->
<style>
    .map-container {
        height: 200px; /* Feste Höhe für horizontale Darstellung */
        width: 100%; /* Volle Breite des Containers */
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .map-container:hover {
        transform: scale(1.02);
    }

    .content-section {
        padding-top: 1rem; /* Abstand zwischen Karte und Inhalt */
    }

    .text-black {
        color: #333 !important;
        font-size: 1rem;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .map-container {
            height: 150px; /* Kleinere Höhe auf mobilen Geräten */
        }

        .text-black {
            font-size: 0.95rem;
        }
    }

    @media (max-width: 576px) {
        .map-container {
            height: 120px; /* Noch komprimierter für sehr kleine Bildschirme */
        }
    }

    .modal {
        z-index: 1055; /* Über anderen Inhalten */
    }

    .modal-backdrop {
        z-index: 1050; /* Hinter dem Modal, aber über dem Rest */
    }

    .modal-content {
        position: relative;
        z-index: 1055;
    }

    body.modal-open {
        overflow: hidden; /* Verhindert Scrollen im Hintergrund */
    }
</style>
