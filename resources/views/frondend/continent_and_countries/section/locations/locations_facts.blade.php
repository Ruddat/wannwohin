<section class="section section-no-border continent-facts-section m-0 pb-0">
    <div class="container">
        <div class="row g-4 align-items-center">
            <!-- Textbereich -->
            <div class="col-12 col-lg-7">
                <div class="card h-100 custom-border continent-facts-card">
                    <div class="card-body p-4">
                        @if(!empty($country->country_text))
                            <p class="card-text">@autotranslate($country->country_text, app()->getLocale())</p>
                        @else
                            <div class="placeholder-image d-flex align-items-center justify-content-center">
                                <h4 class="text-uppercase text-white">
                                    @autotranslate($country->title, app()->getLocale())
                                </h4>
                            </div>
                        @endif
                    </div>
                </div>
            </div>


            <!-- Info-Karte -->
            <div class="col-12 col-lg-5">
                <div class="card h-100 continent-facts-card">
                    <div class="card-header">
                        <h4 class="text-uppercase mb-0">@autotranslate($country->title, app()->getLocale())</h4>
                    </div>
                    <div class="card-body">

                        <div class="text-center mb-3">
                            <div class="continent-flag" style="background-image: url('{{ asset("assets/flags/4x3/" . strtolower($country->country_code) . ".svg") }}');"></div>
                        </div>
                        <table class="table table-borderless continent-facts-table">
                            <tr>
                                <td>
                                    <span>@autotranslate("Währung", app()->getLocale())</span>
                                    <div><h5>@autotranslate($country->currency_code, app()->getLocale())</h5></div>
                                </td>
                                <td>
                                    <span>@autotranslate("Hauptstadt", app()->getLocale())</span>
                                    <div><h5>@autotranslate($country->capital, app()->getLocale())</h5></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>@autotranslate("Preistendenz", app()->getLocale())</span>
                                    <div><h5>@autotranslate($country->price_tendency, app()->getLocale())</h5></div>
                                </td>
                                <td>
                                    <span>
                                        <?php
                                            $languageLabel = count(explode(',', $country->official_language)) > 1 ? 'Sprachen' : 'Sprache';
                                            echo app('autotranslate')->trans($languageLabel, app()->getLocale());
                                        ?>
                                    </span>
                                    <div><h5>@autotranslate($country->official_language, app()->getLocale())</h5></div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <span>@autotranslate("Visum & Reisepass", app()->getLocale())</span>
                                    <div>
                                        <h5>
                                            <?php
                                                $visumText = $country->country_visum_needed
                                                    ? 'Nicht nötig'
                                                    : ($country->country_visum_max_time ?? 'Keine Angaben');
                                                echo app('autotranslate')->trans($visumText, app()->getLocale());
                                            ?>
                                        </h5>
                                    </div>
                                </td>
<!-- Travel Warning -->
@if ($country->travelWarning)
    <div class="travel-warning-indicator d-flex align-items-center mb-3">
        <!-- Ampel -->
        <div class="traffic-light me-3">
            <div class="light red {{ $country->travelWarning->severity == 'Level 4: Do Not Travel' ? 'active' : '' }}"></div>
            <div class="light orange {{ $country->travelWarning->severity == 'Level 3: Reconsider Travel' ? 'active' : '' }}"></div>
            <div class="light yellow {{ $country->travelWarning->severity == 'Level 2: Exercise Increased Caution' ? 'active' : '' }}"></div>
            <div class="light green {{ $country->travelWarning->severity == 'Level 1: Exercise Normal Precautions' ? 'active' : '' }}"></div>
        </div>
        <!-- Text -->
        <div class="travel-warning-text">
            <strong>@autotranslate('Reisewarnung', app()->getLocale()):</strong>
            <span>
                @autotranslate($country->travelWarning->severity, app()->getLocale())
            </span>
            <br>
            <small>
                <strong>@autotranslate('Stand:', app()->getLocale())</strong>
                {{ \Carbon\Carbon::parse($country->travelWarning->issued_at)->format('d.m.Y') }}
            </small>
        </div>
    </div>
@endif



                            </tr>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>


<style>
/* Hauptbereich */
.continent-facts-section {
    background-color: #eaeff5 !important;
}

/* Karten */
.continent-facts-card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    background-color: #fff;
}

.continent-facts-card .card-header {
    background-color: #dbdbdb !important;
    text-align: center;
    padding: 16px;
}

.continent-facts-card .card-body {
    padding: 16px;
}

/* Flagge */
.continent-flag {
    background-size: cover;
    background-position: center;
    width: 120px; /* Etwas größere Größe für plakettenähnliches Aussehen */
    height: 90px;
    border: 3px solid #ddd; /* Leicht verstärkter Rand */
    border-radius: 12px; /* Runde Ecken für plakettenähnlichen Effekt */
    margin: -36px auto 16px auto;
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15); /* Leichtes Schatten für 3D-Effekt */
}

/* Tabelle */
.continent-facts-table td {
    text-align: center;
    padding: 0.75rem;
}

.continent-facts-table span {
    color: #6c757d; /* Muted Text */
}

.continent-facts-table h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: bold;
}

/* Responsiv */
@media (max-width: 768px) {
    .continent-facts-card {
        text-align: center;
    }

    .continent-facts-card .card-body p {
        font-size: 0.9rem;
    }

    .continent-flag {
        width: 100px;
        height: 75px;
        margin: -30px auto 12px auto;
    }
}


.traffic-light {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 30px; /* Kleinere Ampel */
    background-color: #333;
    border-radius: 5px;
    padding: 5px;
}

.light {
    width: 20px;
    height: 20px;
    margin: 3px 0; /* Weniger Abstand */
    border-radius: 50%;
    background-color: #aaa;
    transition: background-color 0.3s;
}

.light.active {
    background-color: currentColor;
}

.light.red {
    color: #e74c3c;
}

.light.orange {
    color: #e67e22;
}

.light.yellow {
    color: #f1c40f;
}

.light.green {
    color: #2ecc71;
}

.travel-warning-text {
    font-size: 0.9rem;
    color: #333;
    line-height: 1.2;
}

.travel-warning-indicator {
    font-size: 0.85rem; /* Kleinere Schrift für den gesamten Bereich */
    background-color: #f9f9f9; /* Dezentes Hintergrund-Highlight */
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
}


/* Platzhalterbild */
.placeholder-image {
    background-image: url('{{ $country->image1_path ?? asset('path/to/placeholder.jpg') }}');
    background-size: cover;
    background-position: center;
    width: 100%;
    height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    text-shadow: 0 1px 4px rgba(0, 0, 0, 0.8);
}

.placeholder-image h4 {
    font-size: 3.5rem;
    font-weight: bold;
    margin: 0;
}

</style>
