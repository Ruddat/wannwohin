<div id="about-me" class="about-me-section">
    <section class="section bg-gradient-light m-0">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <!-- Texte -->
                <div class="col-lg-6">
                    <h2
                        class="text-color-dark font-weight-extra-bold text-uppercase mb-4 animate__animated animate__fadeIn">
                        @autotranslate('Ihr Online-Reiseführer', app()->getLocale())
                    </h2>
                    <p class="lead text-color-grey animate__animated animate__fadeIn" style="animation-delay: 0.2s;">
                        @autotranslate('Willkommen bei <strong>www.wann-wohin.de</strong> – Ihrem Begleiter für die perfekte Reise! Träumen Sie von einem Urlaub, wissen aber noch nicht, wohin es gehen soll? Wir haben für Sie eine bunte Auswahl an Inspirationen zusammengestellt, damit Sie spielend leicht Ihr Traumziel entdecken.', app()->getLocale())
                    </p>
                    <p class="text-color-grey animate__animated animate__fadeIn" style="animation-delay: 0.4s;">
                        @autotranslate('Unsere über 150 Reiseziele sind in übersichtliche Kategorien unterteilt – scrollen Sie einfach weiter, um sie zu entdecken. Oder nutzen Sie unseren praktischen Reise-Wizard für eine maßgeschneiderte Suche. Ihr nächstes Abenteuer wartet schon – lassen Sie sich inspirieren!', app()->getLocale())
                    </p>
                    <p class="text-color-grey font-weight-bold animate__animated animate__fadeIn"
                        style="animation-delay: 0.6s;">
                        @autotranslate('Wir wünschen Ihnen viel Freude bei der Reiseplanung – Ihr Team von wann-wohin.de', app()->getLocale())
                    </p>
                    <a href="{{ route('explore') }}" class="btn btn-explore mt-3 animate__animated animate__pulse" id="explore-btn" style="animation-delay: 0.8s;">
                        @autotranslate('Finde jetzt dein Abenteuer!', app()->getLocale())
                    </a>
                    <p class="text-color-grey small mt-2">
                        @autotranslate('Keine Idee? Wir finden spontan dein Ziel!', app()->getLocale())
                    </p>
                </div>

                <!-- Top 10 Box -->
                <div class="col-lg-6">
                    <div class="top-ten-box bg-white p-4 rounded shadow-lg aanimate__backInRight"

                        style="animation-delay: 0.4s;">
                        <h4 class="text-center text-color-dark font-weight-bold mb-4">
                            🌟 @autotranslate('Top 10 Reiseziele', app()->getLocale())
                        </h4>
                        @if (!empty($top_ten))
                            <!-- Desktop-Tabelle -->
                            <div class="d-none d-md-block table-responsive">
                                <table class="table table-borderless align-middle mb-0">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-start">@autotranslate('Reiseziel', app()->getLocale())</th>
                                            <th class="text-center">@autotranslate('Temp', app()->getLocale())</th>
                                            <th class="text-center">@autotranslate('Wetter', app()->getLocale())</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (collect($top_ten)->slice(0, 10) as $location)
                                            <tr class="border-bottom hover-row">
                                                <td class="fw-bold text-center">{{ $loop->iteration }}.</td>
                                                <td class="text-start">
                                                    <a href="{{ route('location.details', [
                                                        'continent' => $location['continent'] ?? 'unknown',
                                                        'country' => $location['country'] ?? 'unknown',
                                                        'location' => $location['location_alias'] ?? 'unknown',
                                                    ]) }}"
                                                        class="text-dark text-decoration-none d-flex align-items-center gap-2"
                                                        data-bs-toggle="tooltip" title="@autotranslate($location['location_title'], app()->getLocale())">
                                                        <img src="{{ asset('assets/flags/4x3/' . strtolower($location['iso2']) . '.svg') }}"
                                                            alt="@autotranslate($location['location_title'], app()->getLocale())" class="flag-icon rounded">
                                                        <span class="location-title">
                                                            @autotranslate($location['location_title'], app()->getLocale())
                                                        </span>
                                                    </a>
                                                </td>
                                                <td class="text-center">
                                                    {{ $location['climate_data']['daily_temperature'] ?? 'N/A' }}°C</td>
                                                <!-- Desktop-Tabelle -->
                                                <td class="text-center">
                                                    @if (!empty($location['climate_data']['icon']))
                                                        <img src="{{ asset('weather-icons/' . $location['climate_data']['icon'] . '.png') }}"
                                                            alt="{{ $location['climate_data']['weather_description'] ?? '-' }}"
                                                            class="weather-icon" data-bs-toggle="tooltip"
                                                            title="{{ $location['climate_data']['weather_description'] ?? '-' }}">
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- Mobile-Karten -->
                            <div class="d-md-none">
                                @foreach (collect($top_ten)->slice(0, 10) as $location)
                                    <div class="card mb-3 border-0 shadow-sm hover-card">
                                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-bold">{{ $loop->iteration }}.</span>
                                                <a href="{{ route('location.details', [
                                                    'continent' => $location['continent'] ?? 'unknown',
                                                    'country' => $location['country'] ?? 'unknown',
                                                    'location' => $location['location_alias'] ?? 'unknown',
                                                ]) }}"
                                                    class="text-dark text-decoration-none d-flex align-items-center gap-2">
                                                    <img src="{{ asset('assets/flags/4x3/' . strtolower($location['iso2']) . '.svg') }}"
                                                        alt="@autotranslate($location['location_title'], app()->getLocale())" class="flag-icon rounded">
                                                    <span class="location-title">
                                                        @autotranslate($location['location_title'], app()->getLocale())
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span>{{ $location['climate_data']['daily_temperature'] ?? 'N/A' }}°C</span>
                                                @if (!empty($location['climate_data']['icon']))
                                                    <img src="{{ asset('weather-icons/' . $location['climate_data']['icon'] . '.png') }}"
                                                        alt="{{ $location['climate_data']['weather_description'] ?? '-' }}"
                                                        class="weather-icon" data-bs-toggle="tooltip"
                                                        title="{{ $location['climate_data']['weather_description'] ?? '-' }}">
                                                @else
                                                    <span>-</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info text-center rounded">
                                @autotranslate('Bald gibt’s hier die Top-10-Reiseziele mit aktuellen Wetterdaten!', app()->getLocale())
                            </div>
                        @endif
                    </div>
                </div>


            </div>
        </div>
    </section>
</div>


<style scoped>
    /* Allgemein */
    .about-me-section {
        background: linear-gradient(135deg, #f5f7fa 0%, #e2e8f0 100%);
        font-family: 'Poppins', sans-serif;
    }

    .bg-gradient-light {
        background: transparent;
    }

    /* Texte */
    .text-color-dark {
        color: #2d3748 !important;
    }

    .text-color-grey {
        color: #4a5568 !important;
        line-height: 1.6;
    }

    .btn-primary {
        background: #3182ce;
        border: none;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: #2b6cb0;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(49, 130, 206, 0.3);
    }

    /* Top 10 Box */
    .top-ten-box {
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
    }

    .top-ten-box:hover {
    }

    .top-ten-box h4 {
        font-size: 1.5rem;
        color: #2d3748;
    }

    .table th {
        background: #3182ce;
        color: white;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 10px;
    }

    .table td {
        padding: 12px;
        vertical-align: middle;
        font-size: 0.9rem;
        color: #4a5568;
    }

    .hover-row:hover {
        background: #f7fafc;
        transition: background 0.2s ease;
    }

    .flag-icon {
        width: 24px;
        height: auto;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .weather-icon {
        width: 26px;
        height: auto;
    }

    .location-title {
        font-size: 1.0rem;
        /* Angepasst auf 1.2rem wie gewünscht */
        font-weight: 600;
        color: #2d3748;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }

    /* Mobile-Karten */
    .card {
        background: #ffffff;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .card-body {
        font-size: 0.9rem;
    }

    .hover-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    /* Animationen */
    .animate__animated {
        animation-duration: 1s;
    }

    .animate__pulse {
        animation-iteration-count: infinite;
    }

    /* Responsivität */
    @media (max-width: 768px) {
        .top-ten-box {
            padding: 12px;
        }

        .top-ten-box h4 {
            font-size: 1.25rem;
        }

        .flag-icon,
        .weather-icon {
            width: 20px;
        }

        .location-title {
            font-size: 0.8rem;
            /* Etwas kleiner auf Mobilgeräten */
            max-width: 150px;
        }

        .card-body {
            font-size: 0.85rem;
        }

        .btn-primary {
            padding: 10px 20px;
            font-size: 0.9rem;
        }

        .lead {
            font-size: 1.1rem;
        }
    }


    .btn-explore {
    background: linear-gradient(135deg, #ff6b6b, #ff8e53); /* Auffälliger Farbverlauf */
    border: none;
    padding: 14px 35px;
    border-radius: 30px;
    font-weight: 700;
    font-size: 1.2rem;
    color: white;
    text-transform: uppercase;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
    transition: all 0.3s ease;
}

.btn-explore:hover {
    background: linear-gradient(135deg, #ff8e53, #ff6b6b);
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(255, 107, 107, 0.6);
}

.animate__pulse {
    animation-iteration-count: infinite;
    animation-duration: 1.5s;
}

</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const exploreBtn = document.getElementById('explore-btn');

        // Funktion zum Abrufen des Standorts
        function getUserLocation(callback) {
            if (!navigator.geolocation) {
                console.warn('Geolocation wird vom Browser nicht unterstützt.');
                localStorage.setItem('userLocation', 'not_supported');
                callback(null);
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    const location = { lat, lon };
                    localStorage.setItem('userLocation', JSON.stringify(location));
                    callback(location);
                },
                (error) => {
                    console.error('Standortfehler:', error.message);
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            localStorage.setItem('userLocation', 'denied');
                            break;
                        case error.POSITION_UNAVAILABLE:
                            localStorage.setItem('userLocation', 'unavailable');
                            break;
                        case error.TIMEOUT:
                            localStorage.setItem('userLocation', 'timeout');
                            break;
                        default:
                            localStorage.setItem('userLocation', 'error');
                    }
                    callback(null);
                },
                {
                    timeout: 10000, // Erhöhe Timeout auf 10 Sekunden
                    maximumAge: 60000, // Akzeptiere Standortdaten, die bis zu 60 Sekunden alt sind
                    enableHighAccuracy: false // Keine hohe Genauigkeit erforderlich, spart Zeit
                }
            );
        }

        // Standort beim Laden abrufen, falls nicht vorhanden
        if (!localStorage.getItem('userLocation')) {
            getUserLocation((location) => {
                // Optional: Hier könntest du etwas tun, wenn der Standort abgerufen wurde
            });
        }

        // Button-Klick: Standort verwenden oder ohne Standort weiterleiten
        exploreBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const storedLocation = localStorage.getItem('userLocation');

            if (storedLocation && storedLocation !== 'not_available' && storedLocation !== 'denied' && storedLocation !== 'unavailable' && storedLocation !== 'timeout' && storedLocation !== 'error' && storedLocation !== 'not_supported') {
                try {
                    const { lat, lon } = JSON.parse(storedLocation);
                    window.location.href = `/explore?lat=${lat}&lon=${lon}`;
                } catch (err) {
                    console.error('Fehler beim Parsen der Standortdaten:', err);
                    window.location.href = '/explore';
                }
            } else {
                // Optional: Benutzer informieren, warum kein Standort verwendet wird
                console.log('Kein gültiger Standort verfügbar, leite ohne Standort weiter.');
                window.location.href = '/explore';
            }
        });
    });
    </script>
