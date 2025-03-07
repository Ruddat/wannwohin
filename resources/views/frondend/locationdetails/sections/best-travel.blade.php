<div class="container my-4">
    <article class="timeline-box right custom-box-shadow-2 box-shadow-2 position-relative">
        <div class="row align-items-center">
            <!-- Image Section -->
            <div class="experience-info col-lg-3 col-sm-12 bg-color-primary p-0 article-img"
                 style="background-image: url('https://www.wann-wohin.de/assets/img/location/lage-klima.jpg'); background-size: cover; background-position: center; min-height: 200px;">
            </div>

            <!-- Description Section -->
            <div class="experience-description col-lg-9 col-sm-12 bg-color-light px-4 py-4 rounded-end shadow-sm">
                <h4 class="text-color-dark font-weight-semibold mb-3">Beste Reisezeit {{ $location->title }}</h4>

                <div class="formatted-text text-muted mb-3">
                    {!! app('autotranslate')->trans($location->text_best_traveltime, app()->getLocale()) !!}
                </div>

                <p class="text-black fw-bold mb-2">
                    Die beste Reisezeit, um {{ $location->title }} kennenzulernen, sind die folgenden Monate:
                </p>

                <!-- Monats-Kalender Karten -->
                <div class="d-flex flex-wrap gap-3 justify-content-start">
                    @foreach($best_travel_months as $index => $month)
                        <div class="text-center month-card position-relative"
                             data-bs-toggle="tooltip"
                             data-bs-placement="top"
                             title="Perfekt für Sightseeing und mildes Wetter">
                            <img src="{{ asset('img/best_travel_time/' . $index . '.png') }}"
                                 alt="Reisezeit Monat {{ $month }}"
                                 class="img-fluid rounded shadow-sm transition-effect"
                                 style="max-width: 70px; cursor: pointer;">
                            <p class="mt-2 text-dark">@autotranslate($month, app()->getLocale())</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </article>
</div>

<!-- Zusätzliches CSS -->
<style>
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
    @media (max-width: 576px) {
        .experience-info, .experience-description {
            width: 100%;
        }
        .month-card img {
            max-width: 50px;
        }
    }
</style>

<script>
    // Tooltip aktivieren
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Optional: Klick-Interaktion für Monate
    document.querySelectorAll('.month-card').forEach(card => {
        card.addEventListener('click', () => {
            alert('Hier könnten Details zu ' + card.querySelector('p').textContent + ' erscheinen!');
        });
    });
</script>
