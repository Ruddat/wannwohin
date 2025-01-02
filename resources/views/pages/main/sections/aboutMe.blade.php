<div id="about-me">
    <section class="section section-no-border bg-color-light m-0">
        <div class="container">
            <div class="row">
                <!-- Linke Spalte: Texte -->
                <div class="col-lg-6 mb-4">
                    <h2 class="text-color-dark font-weight-extra-bold text-uppercase">ONLINE REISEFÜHRER</h2>
                    <p>Herzlich willkommen bei www.wann-wohin.de – Ihrem Online Reiseführer. Sie haben Urlaub und Lust auf einen Tapetenwechsel – wissen aber nicht wohin die nächste Reise gehen soll? Wir haben auf dieser Seite unterschiedliche Möglichkeiten für Sie zusammengestellt, mit denen Sie schnell und unkompliziert Ihr Traumziel finden.</p>
                    <p>Für Unentschlossene haben wir über 150 Urlaubsziele in verschiedene Kategorien eingeteilt. Diese finden Sie etwas weiter unten. Mit einem Klick geht’s auch schon los. Wenn Sie aber schon spezielle Vorstellungen von Ihrem Urlaub haben, können Sie direkt mit der Detailsuche von unserem Reise-Wizard starten. So finden auch Sie garantiert Ihr Traumziel – probieren Sie es einfach aus.</p>
                    <p>Das Team von wann-wohin wünscht Ihnen viel Spaß bei der Suche nach Ihrem nächsten Urlaubsziel.</p>
                </div>

<!-- Rechte Spalte: Top 10 Box -->
<!-- Rechte Spalte: Top 10 Box -->
<div class="col-lg-6">
    <div class="custom-box-details bg-color-light custom-box-shadow-1 p-4">
        <h4 class="text-center text-color-dark font-weight-bold mb-4">🌍 Top 10 Reiseziele</h4>
        @if (!empty($top_ten)) <!-- Prüfen, ob das Array nicht leer ist -->
        <div class="table-responsive">
            <table class="table table-borderless align-middle text-center mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>#</th>
                        <th>Reiseziel</th>
                        <th>Temp</th>
                        <th>Wetter</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($top_ten as $location)
                    <tr class="border-bottom">
                        <!-- Position -->
                        <td class="text-primary fw-bold">{{ $loop->iteration }}.</td>

                        <!-- Flagge und Reiseziel -->
                        <td class="d-flex align-items-center">
                            <a href="{{ route('location.details', [
                                'continent' => $location['continent']->alias ?? 'unknown',
                                'country' => $location['country']->alias ?? 'unknown',
                                'location' => $location['location_alias'],
                            ]) }}" data-bs-toggle="tooltip" title="{{ $location['location_title'] }}">

                            <span class="fi fi-{{ strtolower($location['iso2']) }} me-2"></span>
                            <span>{{ $location['location_title'] }}</span>
                            </a>
                        </td>

                        <!-- Temperatur -->
                        <td>
                            <span class="custom-text-color-2 text-nowrap fw-bold">
                                @if(isset($location['climate_data']['daily_temperature']))
                                    {{ intval($location['climate_data']['daily_temperature']) }}°C <!-- Ohne Nachkommastellen -->
                                @else
                                    N/A
                                @endif
                            </span>
                        </td>

                        <!-- Wetterbeschreibung und Icon -->
                        <td class="d-flex align-items-center justify-content-center">
                            @if($location['climate_data']['weather_icon'] ?? false)
                            <img src="{{ $location['climate_data']['weather_icon'] }}" alt="{{ $location['climate_data']['weather_description'] ?? 'N/A' }}" class="me-2" style="height: 30px; width: auto;">
                            @endif
                            <span class="custom-text-color-2">{{ $location['climate_data']['weather_description'] ?? 'N/A' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="alert alert-warning text-center">
            <strong>Keine Daten verfügbar:</strong> Derzeit sind keine Top-10-Reiseziele mit gültigen Wetterdaten verfügbar.
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

    #about-me .table-borderless th {
        font-size: 0.95rem;
    }

    #about-me .table-borderless td {
        font-size: 0.85rem;
        vertical-align: middle;
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
    }

    @media (max-width: 768px) {
        #about-me .custom-box-details {
            padding: 1rem;
        }

        #about-me img {
            height: 20px !important;
        }

        #about-me .table-borderless th,
        #about-me .table-borderless td {
            font-size: 0.8rem;
        }
    }
</style>
