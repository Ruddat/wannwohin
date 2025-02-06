<section class="section section-no-border bg-color-light m-0 pb-0" style="background-color: #eaeff5 !important;">
    <div class="container" style="background-color: #eaeff5">
        <!-- Überschrift -->
        <div class="row mb-4" data-aos="fade-down" data-aos-delay="100">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-uppercase">
                    @autotranslate("{$location->title}: Alles Wichtige auf einen Blick", app()->getLocale())
                </h2>
                <hr class="w-25 mx-auto" style="border: 2px solid #007bff;">
            </div>
        </div>

        <div class="row">
            <!-- Hauptbild -->
            <div class="col-lg-7 col-md-12">
                @php
                    $imagePaths = array_filter([
                        $location->text_pic1,
                        $location->text_pic2,
                        $location->text_pic3,
                    ]);
                    $randomImage = $imagePaths ? $imagePaths[array_rand($imagePaths)] : null;
                @endphp

                <div class="figure-img img-fluid custom-border position-relative"
                     style="background-repeat: no-repeat; background-size: cover; background-position: center;
                            background-image: url('{{ $randomImage ? asset($randomImage) : asset("img/placeholders/location-placeholder.jpg") }}');
                            height: 100%; min-height: 400px;"
                     data-aos="fade-right" data-aos-delay="200">
                    <!-- Schicker Bildtext im unteren Bereich -->
                    <div class="position-absolute bottom-0 w-100 bg-opacity-75 bg-white text-dark p-3 rounded-top shadow-lg">
                        <p class="mb-0 text-center fw-bold">
                            @autotranslate($location->pic1_text ?? 'Kein Beschreibungstext verfügbar', app()->getLocale())
                        </p>
                        @if(!empty($panorama_text_and_style))
                        <div style="background: {{ $panorama_text_and_style['style']['background'] }};
                                    color: {{ $panorama_text_and_style['style']['color'] }};
                                    font-family: {{ $panorama_text_and_style['style']['font'] }};
                                    padding: 20px;
                                    text-align: center;">
                            <p>@autotranslate($panorama_text_and_style['text'], app()->getLocale())</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Faktenkarte -->
            <div class="col-lg-5 col-md-12" data-aos="fade-left" data-aos-delay="300">
                <div class="card">
<!-- Card-Header mit Flagge, Name, Standort und Land -->
<div class="card-header-fact text-center" style="background-color: #f0f0f0; position: relative; padding: 20px;">

    <!-- Titel "FAKTENCHECK" -->
    <h4 class="text-uppercase mb-3 fw-bold">
        @autotranslate('FAKTENCHECK', app()->getLocale())
    </h4>

    <!-- Flex-Container für Name, Flagge und Land -->
    <div class="d-flex justify-content-center align-items-center position-relative" style="gap: 20px;">

        <!-- Linke Seite: Name und Standort -->
        <div class="text-end flex-grow-1">
            <h5 class="mb-0 fw-bold">
                @autotranslate($location->title ?? 'Unbekannter Standort', app()->getLocale())
            </h5>
        </div>

        <!-- Mitte: Flagge -->
        <div class="position-relative" style="z-index: 10;">
            <div class="rounded-circle shadow"
                 style="width: 100px; height: 100px; background-color: white; display: flex; align-items: center; justify-content: center;">
                <img src="{{ asset('assets/flags/4x3/' . strtolower($location->iso2 ?? 'unknown') . '.svg') }}"
                     alt="{{ $location->country->title ?? 'Flagge' }}"
                     class="rounded-circle"
                     style="width: 85px; height: 85px; object-fit: cover;">
            </div>
        </div>

        <!-- Rechte Seite: Land -->
        <div class="text-start flex-grow-1">
            <h5 class="mb-0 fw-bold">
                @autotranslate($location->country->title ?? 'Unbekanntes Land', app()->getLocale())
            </h5>
        </div>

    </div>
</div>


                    <!-- Card-Body -->
                    <div class="card-body bg-white pt-5 box-shadow-2">
                        <table class="table table-sm text-center">
                            <tr>
                                <td>
                                    <strong>@autotranslate('Datum & Uhrzeit', app()->getLocale())</strong>
                                    <div id="live-clock" data-offset="{{ $time_offset ?? 0 }}">
                                        {{ \Carbon\Carbon::now()->format('d.m.Y H:i:s') }}
                                    </div>
                                    <small class="text-muted">
                                        @if ($time_offset !== null && round($time_offset, 1) != 0.0)
                                            ({{ number_format($time_offset, 1, ',', '.') }} @autotranslate('Stunden Zeitverschiebung', app()->getLocale()))
                                        @else
                                            @autotranslate('Keine Zeitverschiebung', app()->getLocale())
                                        @endif
                                    </small>
                                </td>

                                <td>
                                    <strong>@autotranslate('Hauptstadt', app()->getLocale())</strong>
                                    <div>@autotranslate($location->country->capital ?? 'Unbekannt', app()->getLocale())</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>@autotranslate('Zeitverschiebung', app()->getLocale())</strong>
                                    <div>
                                        @if ($time_offset !== null && round($time_offset, 1) != 0.0)
                                            {{ number_format($time_offset, 1, ',', '.') }} @autotranslate('Stunden', app()->getLocale())
                                        @else
                                            @autotranslate('Keine Zeitverschiebung', app()->getLocale())
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        // Überprüfen, ob official_language mehrere Sprachen enthält
                                        $languages = explode(',', $location->country->official_language ?? '');
                                        $languageCount = count(array_filter($languages));
                                    @endphp

                                    <strong>
                                        @if ($languageCount === 1)
                                            @autotranslate('Sprache', app()->getLocale())
                                        @elseif ($languageCount > 1)
                                            @autotranslate('Sprachen', app()->getLocale())
                                        @else
                                            @autotranslate('Sprache', app()->getLocale())
                                        @endif
                                    </strong>

                                    <div>
                                        @if ($languageCount > 0)
                                            @autotranslate(implode(', ', $languages), app()->getLocale())
                                        @else
                                            @autotranslate('Nicht angegeben', app()->getLocale())
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>@autotranslate('Preistendenz', app()->getLocale())</strong>
                                    <a href="#" class="text-color-primary" data-bs-toggle="tooltip" data-bs-animation="false" title="Zur Berechnung vergleichen wir das durchschnittliche pro Kopf Einkommen der verschiedenen Länder mit Deutschland">
                                        <i class="fa fa-question-circle"></i>
                                    </a>
                                    <div>
                                        @if(isset($price_trend['factor']) && isset($price_trend['category']))
                                            <!-- Pipette-ähnliche Anzeige -->
                                            <div class="price-trend-container" style="width: 100%; background: linear-gradient(to right, green, yellow, red); border-radius: 10px; position: relative; height: 20px;">
                                                <div class="price-trend-indicator"
                                                     style="position: absolute;
                                                            top: -5px;
                                                            left: {{ max(0, min(100, ($price_trend['factor'] / 2) * 100)) }}%;
                                                            width: 8px;
                                                            height: 30px;
                                                            background-color: black;
                                                            border-radius: 5px;
                                                            transform: translateX(-50%);">
                                                </div>
                                            </div>
                                            <!-- Tooltip direkt beim Wert -->
                                            <span data-bs-toggle="tooltip" data-bs-animation="false" title="Grün = Sehr günstig, Gelb = Durchschnitt (1.0), Rot = Sehr teuer">
                                                {{ number_format($price_trend['factor'], 2) }} (@autotranslate($price_trend['category'], app()->getLocale()))
                                            </span>
                                        @else
                                            <!-- Fallback-Anzeige für fehlende Daten -->
                                            <span class="text-muted">@autotranslate('Keine Daten verfügbar', app()->getLocale())</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="text-center">
                                    <strong>@autotranslate('Währung', app()->getLocale())</strong>
                                    <div class="fw-bold text-uppercase">
                                        @autotranslate(strtoupper($location->country->currency_code ?? 'N/A'), app()->getLocale())
                                    </div>
                                    <livewire:backend.currency-converter.currency-converter-component
                                    :toCurrency="$location->country->currency_code ?? 'USD'"
                                />
                                </td>
                            </tr>
                            <tr>




                                <td class="text-center">
                                    <strong>@autotranslate('Visum', app()->getLocale())</strong>
                                    <div class="d-flex justify-content-center align-items-center mt-2">
                                        @if ($location->country->country_visum_needed !== null)
                                            @if ($location->country->country_visum_needed)
                                                <i class="fas fa-passport text-success me-2 fs-5"></i>
                                                <span class="fw-bold">@autotranslate('Kein Visum erforderlich', app()->getLocale())</span>
                                            @else
                                                <i class="fas fa-plane-departure text-danger me-2 fs-5"></i>
                                                <span class="fw-bold">{{ $location->country->country_visum_max_time ?? 'N/A' }}</span>
                                            @endif
                                        @else
                                            <i class="fas fa-info-circle text-muted me-2 fs-5"></i>
                                            <span class="fw-bold">@autotranslate('Keine Angaben', app()->getLocale())</span>
                                        @endif
                                    </div>
                                </td>



                                <td>
                                    @php
                                        // Steckertypen aus info extrahieren
                                        $plugTypes = array_map('trim', explode(',', $location->electric->info ?? ''));

                                        // Bilder aus plug_images extrahieren
                                        $imageUrls = array_map('trim', explode(',', $location->electric->plug_images ?? ''));

                                        // Typen mit Bildern verknüpfen
                                        $typeImageMap = [];
                                        foreach ($plugTypes as $index => $type) {
                                            $typeImageMap[trim($type)] = $imageUrls[$index] ?? null;
                                        }
                                    @endphp

                                    <div class="text-center">
                                        <strong class="d-block">@autotranslate('Stromnetz', app()->getLocale())</strong>

                                        <button class="electric-button mt-2" data-bs-toggle="modal" data-bs-target="#electricPowerModal" title="Klicken Sie, um Steckertypen anzuzeigen">
                                            <i class="fa fa-plug"></i> {{ $location->electric->power ?? 'N/A' }}
                                            <span class="arrow-container">
                                                <span class="arrow-icon">➜</span>
                                            </span>
                                        </button>

                                        <p class="text-muted mt-1">
                                            @autotranslate('Verfügbare Steckertypen:', app()->getLocale()) {{ implode(', ', $plugTypes) }}
                                        </p>
                                    </div>
                                </td>


                            </tr>
                            <tr>
                                <td>
                                    <strong>@autotranslate('Flugzeit', app()->getLocale())</strong>
                                    <div>{{ ceil($location->flight_hours ?? 0) }} @autotranslate('Stunden', app()->getLocale())</div>
                                </td>
                                <td>
                                    <strong>@autotranslate('Entfernung', app()->getLocale())</strong>
                                    <div>{{ number_format($location->dist_from_FRA, 0, ",", ".") }} km</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Preise -->
                <div class="card mb-3 box-shadow-2" data-aos="fade-up" data-aos-delay="400">
                    <div class="card-header text-center bg-light">
                        <h5 class="text-uppercase">@autotranslate('Preise', app()->getLocale())</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless text-center mb-0">
                            <tr>
                                <td>
                                    <i class="fa fa-hotel text-primary fa-1x mb-2"></i>
                                    <div class="fw-bold">@autotranslate('Unterkünfte', app()->getLocale())</div>
                                    <div>@autotranslate('ab', app()->getLocale()) {{ number_format($location->price_hotel, 0, ",", ".") }} €</div>
                                </td>
                                <td>
                                    <i class="fa fa-plane text-primary fa-1x mb-2"></i>
                                    <div class="fw-bold">@autotranslate('Flüge', app()->getLocale())</div>
                                    <div>@autotranslate('ab', app()->getLocale()) {{ number_format($location->price_flight, 0, ",", ".") }} €</div>
                                </td>
                                <td>
                                    <i class="fa fa-car text-primary fa-1x mb-2"></i>
                                    <div class="fw-bold">@autotranslate('Mietwagen', app()->getLocale())</div>
                                    <div>@autotranslate('ab', app()->getLocale()) {{ number_format($location->price_rental, 0, ",", ".") }} €</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Aktivitäten -->
                <div data-aos="fade-up" data-aos-delay="500">
                    <x-location-activities :locationId="$location->id" />
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="electricPowerModal" tabindex="-1" aria-labelledby="electricPowerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="electricPowerModalLabel">@autotranslate('Steckertypen', app()->getLocale())</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-wrap justify-content-center align-items-center gap-3">
                        @foreach ($typeImageMap as $type => $imageUrl)
                            <div class="card" style="width: 18rem;">
                                <div class="card-body text-center">
                                    <h6 class="card-title">@autotranslate('Typ', app()->getLocale()) {{ $type }}</h6>
                                    @if ($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="Plug Type {{ $type }}" class="img-fluid rounded">
                                    @else
                                        <p>@autotranslate('Kein Bild verfügbar', app()->getLocale())</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@autotranslate('Schließen', app()->getLocale())</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Styles -->
<style>
    .card-header-fact.text-center {
        background-color: #d1d1d1;
        position: relative;
        height: 130px;
    }

    .price-trend-container {
        width: 100%;
        background: linear-gradient(to right, green, yellow, red);
        border-radius: 10px;
        position: relative;
        height: 20px;
        margin-bottom: 10px;
    }

    .price-trend-indicator {
        position: absolute;
        top: -5px;
        width: 10px;
        height: 30px;
        background-color: black;
        border-radius: 5px;
        transform: translateX(-50%);
        transition: left 0.5s ease-in-out;
    }

    @media (max-width: 768px) {
        .travel-heading-with-bg {
        font-size: 1.2rem;
        padding: 0.4rem 0.8rem;
        text-align: center;
        letter-spacing: 0.1rem;
        margin-top: 4px;
        margin-bottom: 10px;
    }

        .card-header-fact.text-center {
            padding: 10px;
        }

        .price-trend-container {
            height: 15px;
        }

        .price-trend-indicator {
            height: 25px;
        }
    }



</style>


<style>
    .electric-button {
        background: linear-gradient(to bottom, #007bff, #0056b3);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 12px 8px;
        font-weight: bold;
        font-size: 16px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        position: relative;
        overflow: hidden;
    }

    .electric-button:hover {
        transform: translateY(-2px);
        box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.2);
    }

    .electric-button i {
        margin-right: 8px;
    }

    /* Pfeil-Container (weißer Kreis) */
    .arrow-container {
        width: 28px;
        height: 28px;
        background-color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 12px;
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        box-shadow: 0px 3px 5px rgba(0, 0, 0, 0.2);
        position: relative;
        top: -2px; /* Pfeil leicht nach oben verschieben */
        transform: rotate(5deg); /* ❗ Pfeil-Container um 5° drehen */
    }

    /* Pfeil-Styling */
    .arrow-icon {
        font-size: 16px;
        color: #007bff;
        transition: transform 0.3s ease-in-out;
        display: inline-block;
    }

    /* Animation bei Hover */
    .electric-button:hover .arrow-container {
        transform: translateX(5px) rotate(5deg); /* ❗ Dreht sich weiter mit der Bewegung */
        box-shadow: 0px 5px 8px rgba(0, 0, 0, 0.3);
    }
</style>


<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Tooltips initialisieren
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // AOS initialisieren
        AOS.init({
            duration: 1000,
            once: true,
        });
    });
</script>
<script>
    function updateClock() {
        let clockElement = document.getElementById('live-clock');
        let offset = parseFloat(clockElement.getAttribute('data-offset')) || 0; // Hole Zeitverschiebung

        let now = new Date();
        now.setHours(now.getHours() + offset); // Zeitverschiebung hinzufügen

        let formattedTime = now.toLocaleString('de-DE', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        clockElement.innerHTML = formattedTime.replace(',', '');
    }

    setInterval(updateClock, 1000); // Jede Sekunde aktualisieren
    updateClock(); // Direkt starten
</script>
