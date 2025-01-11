<div id="about-me">
    <section class="section section-no-border bg-color-light m-0">
        <div class="container">
            <div class="row">
                <!-- Linke Spalte: Texte -->
                <div class="col-lg-6 mb-4">
                    <h2 class="text-color-dark font-weight-extra-bold text-uppercase">@autotranslate('ONLINE REISEFÜHRER', app()->getLocale())</h2>
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
                        @if (!empty($top_ten)) <!-- Prüfen, ob das Array nicht leer ist -->
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle text-center mb-0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>#</th>
                                        <th>@autotranslate('Reiseziel', app()->getLocale())</th>
                                        <th>@autotranslate('Temp', app()->getLocale())</th>
                                        <th>@autotranslate('Wetter', app()->getLocale())</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($top_ten as $location)
                                    <tr class="border-bottom">
                                        <!-- Position -->
                                        <td class="text-primary fw-bold align-middle">{{ $loop->iteration }}.</td>

                                        <!-- Flagge und Reiseziel -->
                                        <td class="align-middle">
                                            <a href="{{ route('location.details', [
                                                'continent' => $location['continent'] ?? 'unknown', // Angepasste Feldnamen
                                                'country' => $location['country'] ?? 'unknown',     // Angepasste Feldnamen
                                                'location' => $location['location_alias'] ?? 'unknown',
                                            ]) }}" data-bs-toggle="tooltip" title="@autotranslate($location['location_title'], app()->getLocale())" class="d-flex align-items-center text-decoration-none">
                                                <span class="fi fi-{{ strtolower($location['iso2']) }} me-2"></span>
                                                <span>@autotranslate($location['location_title'], app()->getLocale())</span>
                                            </a>
                                        </td>

                                        <!-- Temperatur -->
                                        <td class="align-middle">
                                            <span class="custom-text-color-2 text-nowrap fw-bold">
                                                @if(isset($location['climate_data']['daily_temperature']))
                                                    {{ intval($location['climate_data']['daily_temperature']) }}°C
                                                @else
                                                    @autotranslate('N/A', app()->getLocale())
                                                @endif
                                            </span>
                                        </td>

                                        <!-- Wetterbeschreibung und Icon -->
                                        <td class="align-middle">
                                            <div class="d-flex align-items-left flex-nowrap">
                                                @if($location['climate_data']['weather_icon'] ?? false)
                                                    <img src="{{ $location['climate_data']['weather_icon'] }}" alt="@autotranslate($location['climate_data']['weather_description'] ?? 'N/A', app()->getLocale())" class="me-2" style="height: 30px; width: auto;">
                                                @endif
                                                <span class="custom-text-color-2">
                                                    @autotranslate($location['climate_data']['weather_description'] ?? 'N/A', app()->getLocale())
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-warning text-center">
                            <strong>@autotranslate('Keine Daten verfügbar:', app()->getLocale())</strong> @autotranslate('Derzeit sind keine Top-10-Reiseziele mit gültigen Wetterdaten verfügbar.', app()->getLocale())
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<style>
    #about-me .custom-box-details {
        font-size: 0.9rem;
        border-radius: 10px;
        background: #f9f9f9;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    #about-me .table-responsive {
        overflow-x: auto; /* Ermöglicht horizontales Scrollen auf kleinen Bildschirmen */
    }

    #about-me .table-borderless th {
        font-size: 0.95rem;
        vertical-align: middle; /* Sicherstellen, dass alle Zellen vertikal zentriert sind */
    }

    #about-me .table-borderless td {
        font-size: 0.85rem;
        vertical-align: middle;
        white-space: nowrap; /* Verhindert, dass der Text in der Zelle umgebrochen wird */
    }

    #about-me .table-borderless tbody tr {
        transition: background-color 0.3s ease;
    }

    #about-me .table-borderless tbody tr:hover {
        background-color: #f1f1f1;
    }

    #about-me img {
        border-radius: 4px;
        object-fit: cover;
        display: inline-block; /* Stellt sicher, dass die Bilder inline angezeigt werden */
        max-width: 100%; /* Verhindert, dass die Bilder zu groß werden */
        height: 30px; /* Feste Höhe für die Wetter-Icons */
        width: auto; /* Breite automatisch anpassen */
    }

    @media (max-width: 768px) {
        #about-me .custom-box-details {
            padding: 1rem;
        }

        #about-me img {
            height: 20px !important; /* Kleinere Icons auf mobilen Geräten */
        }

        #about-me .table-borderless th,
        #about-me .table-borderless td {
            font-size: 0.8rem; /* Kleinere Schriftgröße auf mobilen Geräten */
        }

        #about-me .table-borderless td {
            padding: 0.5rem; /* Weniger Abstand in den Zellen auf mobilen Geräten */
        }
    }
</style>
