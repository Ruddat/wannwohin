<section class="section section-no-border bg-color-light m-0 pb-0" style="background-color: #eaeff5 !important;">
    <div class="container" style="background-color: #eaeff5">
        <!-- Überschrift -->
        <div class="row mb-4">
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

@php
    //dd($location);

@endphp

                <div class="figure-img img-fluid custom-border position-relative"
                     style="background-repeat: no-repeat; background-size: cover; background-position: center;
                            background-image: url('{{ $randomImage ? asset($randomImage) : asset("img/placeholders/location-placeholder.jpg") }}');
                            height: 100%; min-height: 400px;">
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
            <div class="col-lg-5 col-md-12">

                <div class="card">
                    <!-- Leicht grauer Card-Header -->
                    <div class="card-header-fact text-center" style="background-color: #f0f0f0; position: relative;">
                        <!-- Titel "FAKTENCHECK" -->
                        <h4 class="text-uppercase mb-0 fw-bold" style="padding-top: 20px;">
                            @autotranslate('FAKTENCHECK', app()->getLocale())
                        </h4>
                        <!-- Aussparung für die Flagge -->
<!-- Aussparung für die Flagge -->
<div class="position-absolute start-50 translate-middle"
     style="top: 100%; background-color: white; border-radius: 50%; width: 100px; height: 100px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); z-index: 10;">
    <img src="{{ asset('assets/flags/4x3/' . strtolower($location->iso2 ?? 'unknown') . '.svg') }}"
         alt="{{ $location->country->title ?? 'Flagge' }}"
         class="rounded-circle shadow"
         style="width: 90px; height: 90px; object-fit: cover; margin: 5px;">
</div>
                    </div>

                    <!-- Card-Body -->
                    <div class="card-body bg-white pt-5 box-shadow-2">
                        <table class="table table-sm text-center">
                            <tr>
                                <td>
                                    <strong>@autotranslate('Datum & Uhrzeit', app()->getLocale())</strong>
                                    <div>
                                        {{ $current_time ? \Carbon\Carbon::parse($current_time)->format('d.m.Y H:i') : 'Nicht verfügbar' }}
                                    </div>
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
                                        @if ($time_offset !== null)
                                            {{ number_format($time_offset, 1, ',', '.') }} @autotranslate('Stunden', app()->getLocale())
                                        @else
                                            @autotranslate('Nicht verfügbar', app()->getLocale())
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @php
                                        // Überprüfen, ob official_language mehrere Sprachen enthält (z. B. durch Kommas getrennt oder JSON)
                                        $languages = explode(',', $location->country->official_language ?? ''); // Falls die Sprachen durch Kommas getrennt sind
                                        $languageCount = count(array_filter($languages)); // Filtert leere Einträge heraus
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
                                        <span>
                                            @if($price_trend['factor'])
                                                {{ number_format($price_trend['factor'], 2) }} (@autotranslate($price_trend['category'], app()->getLocale()))
                                            @else
                                                @autotranslate('Keine Daten verfügbar', app()->getLocale())
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <strong>@autotranslate('Währung', app()->getLocale())</strong>
                                    <div>@autotranslate($location->country->currency_code ?? 'N/A', app()->getLocale())</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>@autotranslate('Visum', app()->getLocale())</strong>
                                    <div>
                                        @autotranslate(
                                            $location->country->country_visum_needed !== null
                                                ? ($location->country->country_visum_needed
                                                    ? 'Nicht nötig'
                                                    : ($location->country->country_visum_max_time ?? 'N/A'))
                                                : 'N/A',
                                            app()->getLocale()
                                        )
                                    </div>
                                </td>
                                <td>
                                    @php
                                    // Steckertypen aus `info` extrahieren
                                    $plugTypes = array_map('trim', explode(',', $location->electric->info ?? ''));

                                    // Bilder aus `plug_images` extrahieren
                                    $imageUrls = array_map('trim', explode(',', $location->electric->plug_images ?? ''));

                                    // Typen mit Bildern verknüpfen
                                    $typeImageMap = [];
                                    foreach ($plugTypes as $index => $type) {
                                        $typeImageMap[trim($type)] = $imageUrls[$index] ?? null; // Bild dem Typ zuordnen
                                    }
                                @endphp

                                    <div>
                                        <strong>@autotranslate('Stromnetz', app()->getLocale())</strong>
                                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#electricPowerModal">
                                            {{ $location->electric->power ?? 'N/A' }}
                                        </button>
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
                <div class="card mb-3 box-shadow-2">
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
                <x-location-activities :locationId="$location->id" />
            </div>
        </div>
    </div>

                                    <!-- Modal -->
                                    <div class="modal fade" id="electricPowerModal" tabindex="-1" aria-labelledby="electricPowerModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="electricPowerModalLabel">@autotranslate('Steckdosen', app()->getLocale())</h5>
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
<style>
    .card-header-fact.text-center {
    background-color: #d1d1d1;
    position: relative;
    height: 110px;
}




</style>
<style>
    /* Tooltip-Container */
    .tooltip {
        font-size: 14px;
        background-color: #fff;
        color: #000;
        border: 1px solid #ddd;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 10px;
        border-radius: 5px;
        max-width: 200px; /* Maximale Breite des Tooltips */
        text-align: center;
    }

    /* Tooltip-Inhalt (Bild) */
    .tooltip img {
        max-width: 100%; /* Bild passt sich der Tooltip-Breite an */
        height: auto;    /* Bild bleibt proportional */
        display: block;
        margin: 0 auto;
        border-radius: 5px;
    }
</style>

<style>
    /* Modal immer im Vordergrund */
.modal {
    z-index: 1050; /* Bootstrap-Standardwert für Modals */
}

.modal-backdrop {
    z-index: 1040; /* Hintergrundabdeckung */
}

/* Modal-Header */
.modal-header {
    background-color: #f8f9fa; /* Heller Hintergrund */
    border-bottom: 1px solid #dee2e6;
}

/* Modal-Body-Bilder */
.modal-body img {
    max-width: 100%; /* Bild an Containerbreite anpassen */
    height: auto; /* Proportionen beibehalten */
    margin: 10px auto; /* Abstand zwischen Bildern */
    display: block; /* Zentrierung */
    border-radius: 8px; /* Abgerundete Ecken */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Schattierung */
}

/* Bildkarten im Modal */
.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card-title {
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.card-body {
    padding: 1rem;
    text-align: center;
}

/* Modal-Footer */
.modal-footer {
    border-top: 1px solid #dee2e6;
    background-color: #f8f9fa;
}

</style>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
