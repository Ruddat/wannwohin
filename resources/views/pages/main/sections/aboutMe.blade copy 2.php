<div id="about-me">
    <section class="section section-no-border bg-color-light m-0">


        <h2 class="suggestions-title">🌍 Entdecke dein nächstes Abenteuer!</h2>
        <div class="suggestions-container">
            @foreach ($categories as $key => $title)
                @php
                    $hasSuggestions = isset($suggestions[$title]) && count($suggestions[$title]) > 0;
                @endphp

                @if ($key === 'wetter')
                    <div class="weather-box">
                        <i class="fas fa-sun"></i>
                        <span>{{ $title }}</span>
                        <strong>22°C</strong>
                    </div>
                @else
                    <a href="/{{ $key }}" class="suggestion-button bg-{{ $key }} {{ in_array($key, ['inspiration']) ? 'big' : (in_array($key, ['sport', 'freizeitpark']) ? 'medium' : 'small') }}">
                        <i class="{{ $icons[$key] ?? 'fas fa-question-circle' }}"></i>
                        {{ $title }}
                        @if($hasSuggestions)
                            <span class="badge">{{ count($suggestions[$title]) }}</span>
                        @endif
                    </a>
                @endif
            @endforeach
        </div>


        <div class="container">
            <div class="row">
                <!-- Linke Spalte: Texte und Suchfunktion -->
                <div class="col-lg-6 mb-4">
                    <h2 class="text-color-dark font-weight-extra-bold text-uppercase">
                        @autotranslate('ONLINE REISEFÜHRER', app()->getLocale())
                    </h2>

                    <!-- Suchcontainer -->
                    <div class="search-container p-4 shadow-sm rounded bg-white">
                        <h4 class="text-center font-weight-bold mb-3">
                            @autotranslate('Finde dein Traumziel', app()->getLocale())
                        </h4>

                        <!-- Suchfeld & Zufallsbutton -->
                        <div class="input-group search-bar">
                            <input type="text" class="form-control" placeholder="@autotranslate('Wohin möchtest du reisen?', app()->getLocale())" id="travel-search" autocomplete="off">
                            <button class="btn btn-primary px-3" id="random-destination-btn">
                                🎲 @autotranslate('Ich bin unentschlossen!', app()->getLocale())
                            </button>
                        </div>

                        <!-- Filter für Aktivitäten -->
                        <div class="filter-container mt-3">
                            <label for="filter-activity" class="form-label">
                                @autotranslate('Aktivität wählen:', app()->getLocale())
                            </label>
                            <select class="form-select" id="filter-activity">
                                <option value="">@autotranslate('Alle Aktivitäten', app()->getLocale())</option>
                                <option value="Erlebnis">@autotranslate('Erlebnis', app()->getLocale())</option>
                                <option value="Sport">@autotranslate('Sport', app()->getLocale())</option>
                                <option value="Freizeitpark">@autotranslate('Freizeitpark', app()->getLocale())</option>
                            </select>
                        </div>

                        <!-- Suchergebnisse -->
                        <ul id="search-results" class="list-group search-results mt-2"></ul>
                    </div>

                    <!-- Beschreibungstexte -->
                    <p>
                        @autotranslate('Herzlich willkommen bei www.wann-wohin.de – Ihrem Online Reiseführer. Sie haben Urlaub und Lust auf einen Tapetenwechsel – wissen aber nicht wohin die nächste Reise gehen soll? Wir haben auf dieser Seite unterschiedliche Möglichkeiten für Sie zusammengestellt, mit denen Sie schnell und unkompliziert Ihr Traumziel finden.', app()->getLocale())
                    </p>
                    <p>
                        @autotranslate('Für Unentschlossene haben wir über 150 Urlaubsziele in verschiedene Kategorien eingeteilt. Diese finden Sie etwas weiter unten. Mit einem Klick geht’s auch schon los. Wenn Sie aber schon spezielle Vorstellungen von Ihrem Urlaub haben, können Sie direkt mit der Detailsuche von unserem Reise-Wizard starten. So finden auch Sie garantiert Ihr Traumziel – probieren Sie es einfach aus.', app()->getLocale())
                    </p>
                    <p>
                        @autotranslate('Das Team von wann-wohin wünscht Ihnen viel Spaß bei der Suche nach Ihrem nächsten Urlaubsziel.', app()->getLocale())
                    </p>
                </div>

                <!-- Rechte Spalte: Top 10 Box -->
                <div class="col-lg-6">
                    <div class="custom-box-details bg-color-light custom-box-shadow-1 p-4">
                        <h4 class="text-center text-color-dark font-weight-bold mb-4">
                            🌍 @autotranslate('Top 10 Reiseziele', app()->getLocale())
                        </h4>

                        @if (!empty($top_ten))
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle text-center mb-0">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th>#</th>
                                            <th>@autotranslate('Reiseziel', app()->getLocale())</th>
                                            <th>@autotranslate('Temp', app()->getLocale())</th>
                                            <th>Wetter</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(collect($top_ten)->slice(0, 10) as $location)
                                            <tr class="border-bottom">
                                                <!-- Position -->
                                                <td class="fw-bold align-middle">{{ $loop->iteration }}.</td>

                                                <!-- Flagge & Reiseziel -->
                                                <td class="align-middle">
                                                    <a href="{{ route('location.details', [
                                                        'continent' => $location['continent'] ?? null,
                                                        'country' => $location['country'] ?? null,
                                                        'location' => $location['location_alias'] ?? null,
                                                    ]) }}" class="d-flex align-items-center text-decoration-none text-dark"
                                                    data-bs-toggle="tooltip" title="{{ $location['location_title'] }}">
                                                        <img src="{{ asset('assets/flags/4x3/' . strtolower($location['iso2']) . '.svg') }}"
                                                            alt="{{ $location['location_title'] }}" class="me-2 flag-icon">
                                                        <span class="location-title">
                                                            @autotranslate($location['location_title'], app()->getLocale())
                                                        </span>
                                                    </a>
                                                </td>

                                                <!-- Temperatur -->
                                                <td class="align-middle temp-value">
                                                    {{ $location['climate_data']['daily_temperature'] ?? $translateNA }}°C
                                                </td>

                                                <!-- Wetter-Icon & Tooltip für Wetterbeschreibung -->
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center">
                                                        @if (!empty($location['climate_data']['weather_icon']))
                                                            <img src="{{ $location['climate_data']['weather_icon'] }}"
                                                                alt="{{ $location['climate_data']['weather_description'] ?? '-' }}"
                                                                class="me-2 weather-icon">
                                                        @endif
                                                        <span class="weather-description d-none d-md-inline">
                                                            {{ $location['climate_data']['weather_description'] ?? '-' }}
                                                        </span>
                                                        <span class="d-md-none weather-tooltip" data-bs-toggle="tooltip"
                                                            title="{{ $location['climate_data']['weather_description'] ?? '-' }}">
                                                            <i class="fas fa-info-circle text-primary"></i>
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4">
                                                    <div class="alert alert-warning text-center">
                                                        <strong>@autotranslate('Keine Daten verfügbar', app()->getLocale()):</strong>
                                                        @autotranslate('Derzeit sind keine Top-10-Reiseziele mit gültigen Wetterdaten verfügbar.', app()->getLocale())
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning text-center">
                                <strong>@autotranslate('Keine Daten verfügbar:', app()->getLocale())</strong>
                                @autotranslate('Derzeit sind keine Top-10-Reiseziele mit gültigen Wetterdaten verfügbar.', app()->getLocale())
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


<style>

.suggestions-title {
    text-align: center;
    font-size: 2rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* 🌍 Grid für gleiche Höhe, aber verschiedene Breiten */
.suggestions-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    padding: 20px;
    max-width: 1200px;
    margin: auto;
}

/* 🔘 Basis-Styling für die Buttons */
.suggestion-button {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 30px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
    font-size: 1.5rem;
    color: white;
    text-align: center;
    transition: all 0.3s ease-in-out;
    cursor: pointer;
    min-height: 140px;
}

/* 🎨 Farben für Kategorien */
.bg-erlebnis { background: #9c27b0; } /* Lila */
.bg-sport { background: #4caf50; } /* Grün */
.bg-freizeitpark { background: #e91e63; } /* Pink */
.bg-inspiration { background: #2196f3; } /* Blau */

/* 📏 Unterschiedliche Breiten für ein harmonisches Layout */
.big { grid-column: span 2; } /* Inspiration größer */
.medium { grid-column: span 1; } /* Normale Größe */
.small { grid-column: span 1; } /* Kleinere Kategorien */

/* 🌞 Wetter-Box (statt Button) */
.weather-box {
    background: #fbc02d;
    color: #333;
    text-align: center;
    padding: 15px;
    border-radius: 10px;
    font-size: 1.4rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.weather-box i {
    font-size: 2rem;
    margin-bottom: 5px;
}

/* 🎨 Größere Icons */
.suggestion-button i {
    font-size: 3rem;
    margin-bottom: 10px;
}

/* 🖱️ Hover-Effekte */
.suggestion-button:hover {
    transform: translateY(-5px);
    box-shadow: 0px 10px 15px rgba(0, 0, 0, 0.15);
}

/* 📱 Mobile Optimierung */
@media (max-width: 768px) {
    .suggestions-container {
        grid-template-columns: repeat(2, 1fr); /* 2-Spalten-Layout */
    }
    .big { grid-column: span 2; } /* Inspiration bleibt groß */
    .suggestion-button {
        font-size: 1.2rem;
        padding: 25px;
    }
    .suggestion-button i {
        font-size: 2.5rem;
    }
}
</style>






    </section>
</div>

<!-- JavaScript für Suchfunktion -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("travel-search");
        const resultsContainer = document.getElementById("search-results");
        const randomButton = document.getElementById("random-destination-btn");
        const activityFilter = document.getElementById("filter-activity");

        // Funktion zum Abrufen der Suchergebnisse
        function fetchResults() {
            const query = searchInput.value.trim();
            const activity = activityFilter.value;

            if (query.length < 2 && !activity) {
                resultsContainer.innerHTML = "";
                return;
            }

            const url = `/search-locations?query=${encodeURIComponent(query)}&activity=${encodeURIComponent(activity)}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    resultsContainer.innerHTML = "";

                    if (!data || typeof data !== "object" || !Array.isArray(data.locations)) {
                        console.error("Ungültige API-Antwort:", data);
                        resultsContainer.innerHTML = `<li class="list-group-item text-danger">
                            Fehler beim Laden der Suchergebnisse.
                        </li>`;
                        return;
                    }

                    if (data.locations.length === 0) {
                        resultsContainer.innerHTML = `<li class="list-group-item text-muted">
                            Keine Treffer gefunden.
                        </li>`;
                    }

                    data.locations.forEach(location => {
                        const continent = location.continent_alias ? location.continent_alias : "unknown";
                        const country = location.country_alias ? location.country_alias : "unknown";
                        const travelTime = location.best_traveltime_text !== "N/A" ?
                            `Beste Reisezeit: ${location.best_traveltime_text}` : "";

                        const li = document.createElement("li");
                        li.classList.add("list-group-item");
                        li.innerHTML = `
                            <a href="/details/${continent}/${country}/${location.alias}" class="text-dark text-decoration-none">
                                🌍 ${location.title} (${location.iso2.toUpperCase()})
                            </a>
                            <br>
                            <small class="text-muted">${travelTime}</small>
                        `;
                        resultsContainer.appendChild(li);
                    });
                })
                .catch(error => console.error('Fehler bei Fetch:', error));
        }

        // EventListener für Suche & Dropdown
        searchInput.addEventListener("keyup", fetchResults);
        activityFilter.addEventListener("change", fetchResults);

        // EventListener für "Ich bin unentschlossen"-Button
        randomButton.addEventListener("click", function() {
            fetch(`/random-destination`)
                .then(response => response.json())
                .then(data => {
                    if (data.url) {
                        window.location.href = data.url;
                    } else {
                        console.error("Fehler beim Abrufen des Zufallsziels:", data);
                    }
                })
                .catch(error => {
                    console.error('Fehler bei Zufallsziel:', error);
                    alert("Fehler beim Laden eines zufälligen Reiseziels. Bitte versuche es später erneut.");
                });
        });
    });
</script>

<!-- Stile -->
<style>
    /* Allgemeines Styling */
    #about-me .custom-box-details {
        font-size: 0.9rem;
        border-radius: 10px;
        background: #f9f9f9;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    /* Flaggen */
    .flag-icon {
        width: 24px;
        height: auto;
    }

    /* Wetter-Icons */
    .weather-icon {
        width: 24px;
        height: auto;
    }

    /* Suchcontainer */
    .search-container {
        max-width: 600px;
        margin: 0 auto;
        background: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Suchergebnisse */
    .search-results {
        max-height: 300px;
        overflow-y: auto;
        margin-top: 15px;
        list-style: none;
        padding: 0;
    }

    .search-results li {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 8px;
        transition: background 0.2s, transform 0.2s;
    }

    .search-results li:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }

    /* Mobile-Optimierung */
    @media (max-width: 768px) {
        .search-bar {
            flex-direction: column;
        }

        .filter-container {
            flex-direction: column;
            align-items: center;
        }

        #random-destination-btn {
            width: 100%;
        }
    }
</style>
<style>
    /* 🌍 Verhindert Umbrüche bei langen Reisezielnamen */
.table .location-title {
    max-width: 180px; /* Maximale Breite */
    white-space: nowrap; /* Kein Zeilenumbruch */
    overflow: hidden; /* Überlauf verstecken */
    text-overflow: ellipsis; /* "..." am Ende */
    display: inline-block; /* Notwendig für max-width */
    vertical-align: middle;
}

/* 🌦️ Verhindert Umbrüche bei langen Wetterbeschreibungen */
.table .weather-description {
    max-width: 120px; /* Maximale Breite */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
    vertical-align: middle;
}

/* 🌍 Mobile Optimierung: Falls der Text immer noch zu lang ist, erlauben wir horizontales Scrollen */
@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto; /* Ermöglicht horizontales Scrollen */
    }

    .table .location-title {
        max-width: 140px; /* Kleinere maximale Breite auf kleinen Bildschirmen */
    }

    .table .weather-description {
        max-width: 100px; /* Weniger Platz für Wetterbeschreibung */
    }
}

</style>
