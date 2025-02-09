<div id="about-me">
    <section class="section section-no-border bg-color-light m-0">
        <div class="container">
            <div class="row">
                <!-- Linke Spalte: Texte -->
                <div class="col-lg-6 mb-4">
                    <h2 class="text-color-dark font-weight-extra-bold text-uppercase">@autotranslate('ONLINE REISEFÜHRER', app()->getLocale())</h2>

                    <div class="search-container p-4 shadow-sm rounded bg-white">
                        <h4 class="text-center font-weight-bold mb-3">
                            @autotranslate('Finde dein Traumziel', app()->getLocale())
                        </h4>

                        <!-- Suchfeld & Zufallsbutton -->
                        <div class="input-group search-bar">
                            <input type="text" class="form-control"
                                placeholder="@autotranslate('Wohin möchtest du reisen?', app()->getLocale())"
                                id="travel-search" autocomplete="off">
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


                    <p>
                        @autotranslate('Herzlich willkommen bei www.wann-wohin.de – Ihrem Online Reiseführer. Sie haben Urlaub und Lust auf einen Tapetenwechsel – wissen aber nicht wohin die nächste Reise gehen soll? Wir haben auf dieser Seite unterschiedliche Möglichkeiten für Sie zusammengestellt, mit denen Sie schnell und unkompliziert Ihr Traumziel finden.', app()->getLocale())
                    </p>
                    <p>@autotranslate("Für Unentschlossene haben wir über 150 Urlaubsziele in verschiedene Kategorien eingeteilt. Diese finden Sie etwas weiter unten. Mit einem Klick geht’s auch schon los. Wenn Sie aber schon spezielle Vorstellungen von Ihrem Urlaub haben, können Sie direkt mit der Detailsuche von unserem Reise-Wizard starten. So finden auch Sie garantiert Ihr Traumziel – probieren Sie es einfach aus.", app()->getLocale())</p>
                    <p>@autotranslate("Das Team von wann-wohin wünscht Ihnen viel Spaß bei der Suche nach Ihrem nächsten Urlaubsziel.", app()->getLocale())</p>









                </div>

                <!-- Rechte Spalte: Top 10 Box -->
                <div class="col-lg-6">
                    <div class="custom-box-details bg-color-light custom-box-shadow-1 p-4">
                        <h4 class="text-center text-color-dark font-weight-bold mb-4">🌍 @autotranslate("Top 10 Reiseziele", app()->getLocale())</h4>

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

                                        <!-- Flagge und Reiseziel -->
                                        <td class="align-middle">
                                            <a href="{{ route('location.details', [
                                                'continent' => $location['continent'] ?? null,
                                                'country' => $location['country'] ?? null,
                                                'location' => $location['location_alias'] ?? null,
                                            ]) }}"
                                               class="d-flex align-items-center text-decoration-none text-dark"
                                               data-bs-toggle="tooltip"
                                               title="{{ $location['location_title'] }}">
                                                <img src="{{ asset('assets/flags/4x3/' . strtolower($location['iso2']) . '.svg') }}"
                                                     alt="{{ $location['location_title'] }}"
                                                     class="me-2 flag-icon">
                                                <span class="location-title">
                                                    @autotranslate($location['location_title'], app()->getLocale())
                                                </span>
                                            </a>
                                        </td>

                                        <!-- Temperatur -->
                                        <td class="align-middle">
                                            <span class="text-muted fw-bold">
                                                {{ $location['climate_data']['daily_temperature'] ?? $translateNA }}°C
                                            </span>
                                        </td>

                                        <!-- Wetter -->
<!-- Wetterbeschreibung mit Tooltip -->
<td class="align-middle">
    <div class="d-flex align-items-center">
        @if(!empty($location['climate_data']['weather_icon']))
            <img src="{{ $location['climate_data']['weather_icon'] }}"
                 alt="{{ $location['climate_data']['weather_description'] ?? '-' }}"
                 class="me-2 weather-icon">
        @endif
        <span class="weather-description" data-bs-toggle="tooltip"
              title="{{ $location['climate_data']['weather_description'] ?? '-' }}">
            {{ $location['climate_data']['weather_description'] ?? '-' }}
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
    </section>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let searchInput = document.getElementById("travel-search");
    let resultsContainer = document.getElementById("search-results");
    let randomButton = document.getElementById("random-destination-btn");
    let activityFilter = document.getElementById("filter-activity");

    function fetchResults() {
        let query = searchInput.value.trim();
        let activity = activityFilter.value;

        if (query.length < 2 && !activity) {
            resultsContainer.innerHTML = "";
            return;
        }

        let url = `/search-locations?query=${encodeURIComponent(query)}&activity=${encodeURIComponent(activity)}`;

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
                    let continent = location.continent_alias ? location.continent_alias : "unknown";
                    let country = location.country_alias ? location.country_alias : "unknown";
                    let travelTime = location.best_traveltime_text !== "N/A" ? `Beste Reisezeit: ${location.best_traveltime_text}` : "";

                    let li = document.createElement("li");
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

                data.places?.forEach(place => {
                    let li = document.createElement("li");
                    li.classList.add("list-group-item");
                    li.innerHTML = `<strong>${place.uschrift}</strong><br>
                        <span class="text-muted">${place.text}</span>`;
                    resultsContainer.appendChild(li);
                });
            })
            .catch(error => console.error('Fehler bei Fetch:', error));
    }

    // 🌍 EventListener für Suche & Dropdown
    searchInput.addEventListener("keyup", fetchResults);
    activityFilter.addEventListener("change", fetchResults);

    // 🛫 EventListener für "Ich bin unentschlossen"-Button
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
#about-me .flag-icon {
    height: 20px;
    width: 30px;
    background-color: transparent !important; /* Hintergrund entfernen */
    border: none; /* Rand entfernen */
    border-radius: 4px; /* Abgerundete Ecken */
}

/* Tabellenzellen */
#about-me .table-borderless th,
#about-me .table-borderless td {
    font-size: 0.9rem;
    vertical-align: middle;
    color: #444; /* Dunkelgraue Schriftfarbe */
}

/* Hover-Effekt */
#about-me .table-borderless tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

/* Wetter-Icons */
#about-me .weather-icon {
    height: 30px;
    width: auto;
}

/* Links */
#about-me a {
    color: #444; /* Dunkelgrau */
    text-decoration: none;
}

#about-me a:hover {
    color: #000; /* Schwarz bei Hover */
}

/* Mobile Optimierungen */
@media (max-width: 768px) {
    #about-me .custom-box-details {
        padding: 1rem;
    }

    #about-me .flag-icon {
        height: 15px;
        width: 25px;
    }

    #about-me .table-borderless th,
    #about-me .table-borderless td {
        font-size: 0.8rem;
    }

    #about-me .table-borderless td {
        padding: 0.5rem;
    }
}

</style>

<style>
/* --- 🎨 Haupt-Suchbox Styling --- */
.search-container {
    max-width: 600px;
    margin: 0 auto;
    background: #f8f9fa;
    border-radius: 10px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
}

/* Input-Feld */
.search-bar input {
    border-radius: 8px;
    font-size: 16px;
}

/* --- 🔍 Suchfeld Styling --- */
.search-bar {
    display: flex;
    gap: 10px;
}

#travel-search {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #dfe1e5;
    border-radius: 8px;
    font-size: 16px;
    transition: border 0.3s ease-in-out;
}

#travel-search:focus {
    border-color: #007bff;
    outline: none;
}

/* Button für Zufallssuche */
#random-destination-btn {
    background: linear-gradient(to right, #007bff, #0056b3);
    border: none;
    font-size: 14px;
    transition: all 0.2s ease-in-out;
}

#random-destination-btn:hover {
    background: linear-gradient(to right, #0056b3, #004080);
}

/* --- 🎛️ Filter-Dropdown Styling --- */
.filter-container {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 15px;
    flex-wrap: wrap;
}

.filter-container select {
    padding: 10px;
    font-size: 14px;
    border: 2px solid #dfe1e5;
    border-radius: 8px;
    transition: border 0.3s ease-in-out;
}

.filter-container select:focus {
    border-color: #007bff;
    outline: none;
}

/* --- 📋 Suchergebnisse Styling --- */
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

/* Dropdown */
.filter-container select {
    border-radius: 8px;
    font-size: 14px;
}

/* Suchergebnisse */
.list-group-item {
    font-size: 14px;
    border-left: 4px solid #007bff;
    transition: all 0.2s;
}

.list-group-item:hover {
    background-color: #e9ecef;
    border-left-color: #0056b3;
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
    max-width: 150px; /* Maximale Breite */
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

/* 🏁 Flaggen & Wetter-Icons */
.flag-icon {
    width: 24px;
    height: auto;
}

.weather-icon {
    width: 24px;
    height: auto;
}
</style>
