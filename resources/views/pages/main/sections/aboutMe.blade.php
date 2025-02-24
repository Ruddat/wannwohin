<div id="about-me" class="about-me-section">

    @livewire('frontend.travel-explorer.travel-explorer-component')


    <section class="section bg-color-light m-0">
        <!-- Vorschläge -->
        <h2 class="suggestions-title">🌍 @autotranslate('Entdecke dein nächstes Abenteuer!', app()->getLocale())</h2>
        <div class="suggestions-container">
            @foreach ($categories as $key => $title)
                @php
                    $hasSuggestions = isset($suggestions[$title]) && count($suggestions[$title]) > 0;
                @endphp

                @if ($key === 'wetter')
                    <div class="weather-box">
                        <i class="fas fa-sun"></i>
                        <span>@autotranslate($title, app()->getLocale())</span>
                        <strong>22°C</strong>
                    </div>
                @else
                    <a href="/{{ $key }}"
                       class="suggestion-button bg-{{ $key }} {{ in_array($key, ['inspiration']) ? 'big' : (in_array($key, ['sport', 'freizeitpark']) ? 'medium' : 'small') }}">
                        <i class="{{ $icons[$key] ?? 'fas fa-question-circle' }}"></i>
                        @autotranslate($title, app()->getLocale())
                        @if ($hasSuggestions)
                            <span class="badge">{{ count($suggestions[$title]) }}</span>
                        @endif
                    </a>
                @endif
            @endforeach
        </div>

        <!-- Inhalt -->
        <div class="container">
            <div class="row g-4">
                <!-- Texte -->
                <div class="col-lg-6">
                    <h2 class="text-color-dark font-weight-extra-bold text-uppercase">
                        @autotranslate('ONLINE REISEFÜHRER', app()->getLocale())
                    </h2>
                    <p>@autotranslate('Herzlich willkommen bei www.wann-wohin.de – Ihrem Online Reiseführer. Sie haben Urlaub und Lust auf einen Tapetenwechsel – wissen aber nicht wohin die nächste Reise gehen soll? Wir haben auf dieser Seite unterschiedliche Möglichkeiten für Sie zusammengestellt, mit denen Sie schnell und unkompliziert Ihr Traumziel finden.', app()->getLocale())</p>
                    <p>@autotranslate('Für Unentschlossene haben wir über 150 Urlaubsziele in verschiedene Kategorien eingeteilt. Diese finden Sie etwas weiter unten. Mit einem Klick geht’s auch schon los. Wenn Sie aber schon spezielle Vorstellungen von Ihrem Urlaub haben, können Sie direkt mit der Detailsuche von unserem Reise-Wizard starten. So finden auch Sie garantiert Ihr Traumziel – probieren Sie es einfach aus.', app()->getLocale())</p>
                    <p>@autotranslate('Das Team von wann-wohin wünscht Ihnen viel Spaß bei der Suche nach Ihrem nächsten Urlaubsziel.', app()->getLocale())</p>
                </div>

                <!-- Top 10 Box -->
                <div class="col-lg-6">
                    <div class="top-ten-box bg-color-light p-4">
                        <h4 class="text-center text-color-dark font-weight-bold mb-4">
                            🌍 @autotranslate('Top 10 Reiseziele', app()->getLocale())
                        </h4>
                        @if (!empty($top_ten))
                            <div class="d-none d-md-block table-responsive">
                                <!-- Desktop-Tabelle -->
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
                                            <tr class="border-bottom">
                                                <td class="fw-bold text-center">{{ $loop->iteration }}.</td>
                                                <td class="text-start">
                                                    <a href="{{ route('location.details', [
                                                        'continent' => $location['continent'] ?? 'unknown',
                                                        'country' => $location['country'] ?? 'unknown',
                                                        'location' => $location['location_alias'] ?? 'unknown',
                                                    ]) }}"
                                                       class="text-dark text-decoration-none d-flex align-items-center gap-2"
                                                       data-bs-toggle="tooltip"
                                                       title="@autotranslate($location['location_title'], app()->getLocale())">
                                                        <img src="{{ asset('assets/flags/4x3/' . strtolower($location['iso2']) . '.svg') }}"
                                                             alt="@autotranslate($location['location_title'], app()->getLocale())"
                                                             class="flag-icon">
                                                        <span class="location-title">
                                                            @autotranslate($location['location_title'], app()->getLocale())
                                                        </span>
                                                    </a>
                                                </td>
                                                <td class="text-center">{{ $location['climate_data']['daily_temperature'] ?? 'N/A' }}°C</td>
                                                <td class="text-center">
                                                    @if (!empty($location['climate_data']['weather_icon']))
                                                        <img src="{{ $location['climate_data']['weather_icon'] }}"
                                                             alt="{{ $location['climate_data']['weather_description'] ?? '-' }}"
                                                             class="weather-icon"
                                                             data-bs-toggle="tooltip"
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
                            <div class="d-md-none">
                                <!-- Mobile-Karten -->
                                @foreach (collect($top_ten)->slice(0, 10) as $location)
                                    <div class="card mb-3 border-0 shadow-sm">
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
                                                         alt="@autotranslate($location['location_title'], app()->getLocale())"
                                                         class="flag-icon">
                                                    <span class="location-title">
                                                        @autotranslate($location['location_title'], app()->getLocale())
                                                    </span>
                                                </a>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span>{{ $location['climate_data']['daily_temperature'] ?? 'N/A' }}°C</span>
                                                @if (!empty($location['climate_data']['weather_icon']))
                                                    <img src="{{ $location['climate_data']['weather_icon'] }}"
                                                         alt="{{ $location['climate_data']['weather_description'] ?? '-' }}"
                                                         class="weather-icon"
                                                         data-bs-toggle="tooltip"
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
                            <div class="alert alert-warning text-center">
                                @autotranslate('Derzeit sind keine Top-10-Reiseziele mit gültigen Wetterdaten verfügbar.', app()->getLocale())
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>




<script>
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("travel-search");
    const resultsContainer = document.getElementById("search-results");
    const randomButton = document.getElementById("random-destination-btn");
    const activityFilter = document.getElementById("filter-activity");

    const fetchResults = async () => {
        const query = searchInput?.value.trim() || '';
        const activity = activityFilter?.value || '';
        if (query.length < 2 && !activity) {
            resultsContainer.innerHTML = '';
            return;
        }

        try {
            const response = await fetch(`/search-locations?query=${encodeURIComponent(query)}&activity=${encodeURIComponent(activity)}`);
            const data = await response.json();
            resultsContainer.innerHTML = '';

            if (!Array.isArray(data?.locations)) {
                resultsContainer.innerHTML = '<li class="list-group-item text-danger">Fehler beim Laden der Ergebnisse.</li>';
                console.error('Ungültige API-Antwort:', data);
                return;
            }

            if (data.locations.length === 0) {
                resultsContainer.innerHTML = '<li class="list-group-item text-muted">Keine Treffer gefunden.</li>';
                return;
            }

            data.locations.forEach(location => {
                const continent = location.continent_alias || 'unknown';
                const country = location.country_alias || 'unknown';
                const travelTime = location.best_traveltime_text !== 'N/A' ? `Beste Reisezeit: ${location.best_traveltime_text}` : '';
                resultsContainer.innerHTML += `
                    <li class="list-group-item">
                        <a href="/details/${continent}/${country}/${location.alias}" class="text-dark text-decoration-none">
                            🌍 ${location.title} (${location.iso2.toUpperCase()})
                        </a>
                        <br><small class="text-muted">${travelTime}</small>
                    </li>
                `;
            });
        } catch (error) {
            console.error('Fetch-Fehler:', error);
            resultsContainer.innerHTML = '<li class="list-group-item text-danger">Fehler beim Laden.</li>';
        }
    };

    searchInput?.addEventListener('keyup', fetchResults);
    activityFilter?.addEventListener('change', fetchResults);

    randomButton?.addEventListener('click', async () => {
        try {
            const response = await fetch('/random-destination');
            const data = await response.json();
            if (data.url) window.location.href = data.url;
            else throw new Error('Ungültige Antwort');
        } catch (error) {
            console.error('Random-Fehler:', error);
            alert('Fehler beim Laden eines Zufallsziels.');
        }
    });
});
</script>

<style scoped>
/* Allgemein */
.about-me-section {
    background-color: #f5f7fa;
}

/* Vorschläge */
.about-me-section .suggestions-title {
    text-align: center;
    font-size: 2rem;
    font-weight: bold;
    color: #333;
    margin: 1.5rem 0;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.about-me-section .suggestions-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

.about-me-section .suggestion-button {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.5rem;
    color: white;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    min-height: 140px;
}

.about-me-section .bg-erlebnis { background: #9c27b0; }
.about-me-section .bg-sport { background: #4caf50; }
.about-me-section .bg-freizeitpark { background: #e91e63; }
.about-me-section .bg-inspiration { background: #2196f3; }

.about-me-section .big { grid-column: span 2; }
.about-me-section .medium, .about-me-section .small { grid-column: span 1; }

.about-me-section .weather-box {
    background: #fbc02d;
    color: #333;
    padding: 1rem;
    border-radius: 10px;
    font-size: 1.4rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.about-me-section .suggestion-button i { font-size: 3rem; margin-bottom: 0.5rem; }
.about-me-section .weather-box i { font-size: 2rem; }

.about-me-section .suggestion-button:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.15);
}

/* Top 10 Box */
.about-me-section .top-ten-box {
    font-size: 0.9rem;
    border-radius: 10px;
    background: #f9f9f9;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.about-me-section .flag-icon, .about-me-section .weather-icon {
    width: 24px;
    height: auto;
}

.about-me-section .location-title {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
    vertical-align: middle;
}

.about-me-section .table th,
.about-me-section .table td {
    padding: 0.75rem;
}

.about-me-section .table th.text-start,
.about-me-section .table td.text-start {
    text-align: left;
}

.about-me-section .table th.text-center,
.about-me-section .table td.text-center {
    text-align: center;
}

/* Mobile-Karten */
.about-me-section .card {
    background: #fff;
    border-radius: 8px;
}

/* Responsivität */
@media (max-width: 768px) {
    .about-me-section .suggestions-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        padding: 0.5rem;
    }

    .about-me-section .suggestion-button {
        font-size: 1.2rem;
        padding: 1rem;
        min-height: 120px;
    }

    .about-me-section .suggestion-button i { font-size: 2.5rem; }
    .about-me-section .weather-box { font-size: 1.2rem; padding: 0.75rem; }

    .about-me-section .top-ten-box { font-size: 0.85rem; padding: 1rem; }
    .about-me-section .top-ten-box .card-body { padding: 0.5rem; }
    .about-me-section .flag-icon, .about-me-section .weather-icon { width: 20px; }
}
</style>
