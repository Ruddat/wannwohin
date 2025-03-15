<section class="continent-facts-section section m-0 pb-0">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <!-- Textbereich -->
            <div class="col-12 col-lg-7" data-aos="fade-right" data-aos-duration="500">
                <div class="continent-facts-card card position-relative">
                    <div class="card-body p-4 bg-overlay"
                         style="background-image: url('{{ $country->primaryImage() ?? asset('img/default-country.jpg') }}');">


                         @php
                         // Editor-Inhalt bereinigen
                         function cleanEditorContent(?string $content): ?string {
                             if (is_null($content)) return null;

                             // HTML-Tags entfernen, aber Bilder und Links beibehalten
                             $cleaned = trim(strip_tags($content, '<img><a>'));

                             // Leere Strukturen als null behandeln
                             $emptyPatterns = [
                                 '/^<p>\s*<br\s*\/?>\s*<\/p>$/i', // <p><br></p>
                                 '/^<p>\s*&nbsp;\s*<\/p>$/i', // <p>&nbsp;</p>
                                 '/^\s*$/', // Leere Zeichenketten
                             ];

                             foreach ($emptyPatterns as $pattern) {
                                 if (preg_match($pattern, $content)) {
                                     return null;
                                 }
                             }

                             return empty($cleaned) ? null : $content;
                         }

                         // Bereinigten Text holen
                         $cleanedText = cleanEditorContent($country->country_text ?? null);
                     @endphp

                     @if (!empty($cleanedText))
                         <h4 class="text-uppercase text-white m-0">
                             @autotranslate($country->title, app()->getLocale())
                         </h4>
                         <div class="card-text text-white">
                             @autotranslate($cleanedText, app()->getLocale())
                         </div>
                     @else
                         <div class="placeholder-content d-flex align-items-center justify-content-center h-100">
                             <h4 class="text-uppercase text-white m-0">
                                 @autotranslate($country->title, app()->getLocale())
                             </h4>
                         </div>
                     @endif
                    </div>
                </div>
            </div>

            <!-- Info-Karte -->
            <div class="col-12 col-lg-5" data-aos="fade-left" data-aos-duration="500">
                <div class="continent-facts-card card h-100">
                    <div class="card-header text-center p-3">
                        <h4 class="text-uppercase mb-0">
                            @autotranslate($country->title, app()->getLocale())
                        </h4>
                    </div>
                    <div class="card-body p-3">
                        <div class="continent-flag mx-auto mb-3"></div>
                        <table class="continent-facts-table table table-borderless">
                            <tr>
                                <td>
                                    <span>
                                        <i class="fas fa-coins me-1"></i> @autotranslate('Währung', app()->getLocale())
                                    </span>
                                    <h5>
                                        <i class="fas fa-money-bill-wave me-1 text-primary"></i>
                                        @autotranslate($country->currency_code, app()->getLocale())
                                    </h5>
                                </td>
                                <td>
                                    <span>
                                        <i class="fas fa-city me-1"></i> @autotranslate('Hauptstadt', app()->getLocale())
                                    </span>
                                    <h5>
                                        <i class="fas fa-landmark me-1 text-success"></i>
                                        @autotranslate($country->capital, app()->getLocale())
                                    </h5>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span>
                                        <i class="fas fa-chart-line me-1"></i> @autotranslate('Preistendenz', app()->getLocale())
                                    </span>
                                    <h5>
                                        <i class="fas fa-tag me-1 text-warning"></i>
                                        @autotranslate($country->price_tendency, app()->getLocale())
                                    </h5>
                                </td>
                                <td>
                                    <span>
                                        <i class="fas fa-globe me-1"></i>
                                        @php
                                            $languageLabel = count(explode(',', $country->official_language)) > 1 ? 'Sprachen' : 'Sprache';
                                            echo app('autotranslate')->trans($languageLabel, app()->getLocale());
                                        @endphp
                                    </span>
                                    <h5>
                                        <i class="fas fa-language me-1 text-info"></i>
                                        @autotranslate($country->official_language, app()->getLocale())
                                    </h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center">
                                    <strong>
                                        <i class="fas fa-passport me-1"></i> {{ __('Visum & Reisepass') }}
                                    </strong>
                                    <div class="d-flex justify-content-center align-items-center mt-2">
                                        @if ($country->country_visum_needed === null)
                                            <i class="fas fa-info-circle text-muted me-2 fs-5"></i>
                                            <span class="fw-bold">{{ __('Keine Angaben') }}</span>
                                        @elseif ($country->country_visum_needed)
                                            <i class="fas fa-check-circle text-success me-2 fs-5"></i>
                                            <span class="fw-bold">{{ __('Kein Visum erforderlich') }}</span>
                                        @else
                                            <i class="fas fa-exclamation-triangle text-danger me-2 fs-5"></i>
                                            <span class="fw-bold">
                                                {{ $country->country_visum_max_time ?? __('Keine Angaben') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @if ($country->travelWarning)
                                <tr>
                                    <td colspan="2">
                                        <div class="travel-warning d-flex align-items-center p-2 rounded">
                                            <div class="traffic-light me-3">
                                                @foreach (['Level 4' => 'red', 'Level 3' => 'orange', 'Level 2' => 'yellow', 'Level 1' => 'green'] as $level => $color)
                                                    <div class="light {{ $color }} {{ str_contains($country->travelWarning->severity, $level) ? 'active' : '' }}"></div>
                                                @endforeach
                                            </div>
                                            <div class="travel-warning-text">
                                                <strong>
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    @autotranslate('Reisewarnung:', app()->getLocale())
                                                </strong>
                                                @autotranslate($country->travelWarning->severity, app()->getLocale())
                                                <br>
                                                <small>
                                                    <strong>
                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                        @autotranslate('Stand:', app()->getLocale())
                                                    </strong>
                                                    {{ \Carbon\Carbon::parse($country->travelWarning->issued_at)->format('d.m.Y') }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>




        </div>
    </div>
</section>


{{--
in Npm:
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({ duration: 500, once: true });
</script>
--}}



<style scoped>
.continent-facts-section {
    background-color: #eaeff5;
}

.continent-facts-section .continent-facts-card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    background-color: #fff;
    overflow: visible;
}

.continent-facts-section .bg-overlay {
    background-size: cover;
    background-position: center;
    min-height: 300px;
    position: relative;
}

.continent-facts-section .bg-overlay::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    z-index: 0;
}

.continent-facts-section .bg-overlay > * {
    position: relative;
    z-index: 1;
}

.continent-facts-section .card-text,
.continent-facts-section .placeholder-content {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #fff;
    text-shadow: 0 1px 4px rgba(0, 0, 0, 0.8);
}

.continent-facts-section .placeholder-content h4 {
    font-size: 2.5rem;
    font-weight: bold;
}

.continent-facts-section .card-header {
    background-color: #dbdbdb;
    padding: 12px;
}

.continent-facts-section .continent-flag {
    background: url('{{ asset('assets/flags/4x3/' . strtolower($country->country_code) . '.svg') }}') no-repeat center/cover;
    width: 120px;
    height: 90px;
    border: 3px solid #ddd;
    border-radius: 12px;
    margin: -36px auto 16px;
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
}

.continent-facts-section .continent-facts-table td {
    padding: 0.5rem;
    text-align: center;
}

.continent-facts-section .continent-facts-table span {
    color: #6c757d;
    font-size: 0.9rem;
}

.continent-facts-section .continent-facts-table h5 {
    margin: 0;
    font-size: 1rem;
    font-weight: bold;
}

.continent-facts-section .travel-warning {
    background-color: #fff3e0;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 0.85rem;
}

.continent-facts-section .traffic-light {
    display: flex;
    flex-direction: column;
    width: 20px;
    padding: 5px;
    background-color: #333;
    border-radius: 5px;
}

.continent-facts-section .traffic-light .light {
    width: 14px;
    height: 14px;
    margin: 2px 0;
    border-radius: 50%;
    background-color: #aaa;
    transition: background-color 0.3s;
}

.continent-facts-section .traffic-light .light.active {
    background-color: currentColor;
}

.continent-facts-section .traffic-light .red { color: #e74c3c; }
.continent-facts-section .traffic-light .orange { color: #e67e22; }
.continent-facts-section .traffic-light .yellow { color: #f1c40f; }
.continent-facts-section .traffic-light .green { color: #2ecc71; }

@media (max-width: 768px) {
    .continent-facts-section .bg-overlay {
        min-height: 200px;
    }
    .continent-facts-section .card-text { font-size: 1rem; }
    .continent-facts-section .placeholder-content h4 { font-size: 1.75rem; }
    .continent-facts-section .continent-flag { width: 100px; height: 75px; margin: -30px auto 12px; }
    .continent-facts-section .continent-facts-table td { padding: 0.3rem; }
}
</style>
