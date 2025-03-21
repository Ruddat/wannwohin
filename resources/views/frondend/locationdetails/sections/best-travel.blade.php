<div class="card-body p-4">
    <!-- Image Section (volle Breite oben) -->
    <div class="image-container rounded shadow-lg mb-4"
         style="background-image: url('https://www.wann-wohin.de/assets/img/location/lage-klima.jpg'); background-size: cover; background-position: center; height: 200px; width: 100%;"
         data-aos="fade-down">
    </div>

    <!-- Description Section -->
    <div class="content-section" data-aos="fade-up">
        <h4 class="text-color-dark fw-bold mb-3">
            <i class="fas fa-calendar-alt me-2"></i>
            @autotranslate("Beste Reisezeit für {$location->title}", app()->getLocale())
        </h4>
        <div class="formatted-text text-muted mb-3">
            {!! app('autotranslate')->trans($location->text_best_traveltime, app()->getLocale()) !!}
        </div>
        <p class="text-black fw-bold mb-2">
            Die beste Reisezeit {{ $location->title }} kennenzulernen…
        </p>
        <!-- Monats-Kalender Karten -->
        <div class="d-flex flex-wrap gap-2 justify-content-start">
            @foreach($best_travel_months as $index => $month)
                <div class="text-center month-card position-relative"
                     data-bs-toggle="tooltip"
                     data-bs-placement="top"
                     title="Perfekt für Sightseeing und mildes Wetter">
                    <img src="{{ asset('img/best_travel_time/' . $index . '.png') }}"
                         alt="Reisezeit Monat {{ $month }}"
                         class="img-fluid rounded shadow-sm transition-effect"
                         style="max-width: 60px; cursor: pointer;">
                    <p class="mt-1 text-dark small">@autotranslate($month, app()->getLocale())</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Zusätzliches CSS -->
<style>
    .image-container {
        height: 200px; /* Feste Höhe für horizontale Darstellung */
        width: 100%; /* Volle Breite des Containers */
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .image-container:hover {
        transform: scale(1.02);
    }

    .content-section {
        padding-top: 1rem; /* Abstand zwischen Bild und Inhalt */
    }

    .formatted-text {
        font-size: 1rem;
        line-height: 1.6;
        color: #666;
    }

    .text-black {
        color: #333 !important;
        font-size: 1rem;
    }

    .month-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .month-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .transition-effect {
        transition: all 0.3s ease;
    }

    .small {
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .image-container {
            height: 150px; /* Kleinere Höhe auf mobilen Geräten */
        }

        .formatted-text {
            font-size: 0.95rem;
        }

        .text-black {
            font-size: 0.95rem;
        }

        .month-card img {
            max-width: 50px;
        }

        .small {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .image-container {
            height: 120px; /* Noch komprimierter für sehr kleine Bildschirme */
        }

        .month-card img {
            max-width: 40px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        document.querySelectorAll('.month-card').forEach(card => {
            card.addEventListener('click', () => {
                alert('Hier könnten Details zu ' + card.querySelector('p').textContent + ' erscheinen!');
            });
        });
    });
</script>
