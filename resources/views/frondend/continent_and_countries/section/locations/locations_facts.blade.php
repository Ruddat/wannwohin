<section class="continent-facts-section section m-0 pb-0">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <!-- Textbereich (ohne Parallax und ohne Abdunkeln) -->
            <div class="col-12 col-lg-7" data-aos="fade-right" data-aos-duration="500">
                <div class="continent-facts-card card position-relative card-equal-height">
                    <div class="card-body p-4 bg-overlay" style="background-image: url('{{ $country->primaryImage() ?? asset('img/default-country.jpg') }}');">
                        @php
                            function cleanEditorContent(?string $content): ?string {
                                if (is_null($content)) return null;
                                $cleaned = trim(strip_tags($content, '<img><a>'));
                                $emptyPatterns = [
                                    '/^<p>\s*<br\s*\/?>\s*<\/p>$/i',
                                    '/^<p>\s* \s*<\/p>$/i',
                                    '/^\s*$/',
                                ];
                                foreach ($emptyPatterns as $pattern) {
                                    if (preg_match($pattern, $content)) {
                                        return null;
                                    }
                                }
                                return empty($cleaned) ? null : $content;
                            }
                            $cleanedText = cleanEditorContent($country->country_text ?? null);
                        @endphp
                        @if (!empty($cleanedText))
                            <div class="content-wrapper">
                                <div class="title-wrapper">
                                    <h4 class="text-uppercase text-dark m-0">
                                        @autotranslate($country->title, app()->getLocale())
                                    </h4>
                                </div>
                                <div class="card-text text-white">
                                    @autotranslate($cleanedText, app()->getLocale())
                                </div>
                            </div>
                        @else
                            <div class="placeholder-content d-flex align-items-center justify-content-center h-100">
                                <div class="title-wrapper">
                                    <h4 class="text-uppercase text-dark m-0">
                                        @autotranslate($country->title, app()->getLocale())
                                    </h4>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Info-Karte (mit nebeneinander angeordneten Fakten) -->
            <div class="col-12 col-lg-5" data-aos="fade-left" data-aos-duration="500">
                <div class="continent-facts-card card card-equal-height">
                    <div class="card-header text-center p-3">
                        <h4 class="text-uppercase mb-0">
                            @autotranslate($country->title, app()->getLocale())
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="continent-flag mx-auto mb-4"></div>
                        <div class="fact-list">
                            <div class="fact-row">
                                <div class="fact-item">
                                    <span class="fact-icon"><i class="fas fa-coins"></i></span>
                                    <div class="fact-content">
                                        <span class="fact-label">@autotranslate('Währung', app()->getLocale())</span>
                                        <h5>@autotranslate($country->currency_name, app()->getLocale())</h5>
                                    </div>
                                </div>
                                <div class="fact-item">
                                    <span class="fact-icon"><i class="fas fa-city"></i></span>
                                    <div class="fact-content">
                                        <span class="fact-label">@autotranslate('Hauptstadt', app()->getLocale())</span>
                                        <h5>@autotranslate($country->capital, app()->getLocale())</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="fact-row">
                                <div class="fact-item">
                                    <span class="fact-icon"><i class="fas fa-chart-line"></i></span>
                                    <div class="fact-content">
                                        <span class="fact-label">@autotranslate('Preistendenz', app()->getLocale())</span>
                                        <h5>@autotranslate($country->price_tendency, app()->getLocale())</h5>
                                    </div>
                                </div>
                                <div class="fact-item">
                                    <span class="fact-icon"><i class="fas fa-globe"></i></span>
                                    <div class="fact-content">
                                        <span class="fact-label">
                                            @php
                                                $languageLabel = count(explode(',', $country->official_language)) > 1 ? 'Sprachen' : 'Sprache';
                                                echo app('autotranslate')->trans($languageLabel, app()->getLocale());
                                            @endphp
                                        </span>
                                        <h5>@autotranslate($country->official_language, app()->getLocale())</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="fact-row full-width">
                                <div class="fact-item text-center">
                                    <span class="fact-icon"><i class="fas fa-passport"></i></span>
                                    <div class="fact-content">
                                        <span class="fact-label">{{ __('Visum & Reisepass') }}</span>
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
                                    </div>
                                </div>
                            </div>
                            <div class="fact-row full-width">
                                <div class="fact-item travel-warning-card {{ $country->travelWarning ? 'Level-' . substr($country->travelWarning->severity, 6, 1) : 'no-warning' }}">                                    <span class="fact-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                    <div class="fact-content travel-warning-content">
                                        <span class="fact-label"><strong>@autotranslate('Reisewarnung:', app()->getLocale())</strong></span>
                                        <div class="travel-alert">
                                            <div class="pipette-wrapper">
                                                <div class="pipette">
                                                    <div class="pipette-marker level-4" data-level="Level 4"></div>
                                                    <div class="pipette-marker level-3" data-level="Level 3"></div>
                                                    <div class="pipette-marker level-2" data-level="Level 2"></div>
                                                    <div class="pipette-marker level-1" data-level="Level 1"></div>
                                                </div>
                                                <div class="pipette-arrow"><i class="fas fa-arrow-left"></i></div>
                                            </div>
                                            <div class="travel-warning-text">
                                                @if ($country->travelWarning)
                                                    @autotranslate($country->travelWarning->severity, app()->getLocale())
                                                    <br>
                                                    <small>
                                                        <strong>
                                                            <i class="fas fa-calendar-alt me-1"></i>
                                                            @autotranslate('Stand:', app()->getLocale())
                                                        </strong>
                                                        {{ \Carbon\Carbon::parse($country->travelWarning->issued_at)->format('d.m.Y') }}
                                                    </small>
                                                @else
                                                    @autotranslate('Derzeit keine Warnung bekannt', app()->getLocale())
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    background-color: #f5f7fa; /* Etwas hellerer Hintergrund für Modernität */
    padding: 4rem 0; /* Mehr Weißraum */
}

/* Allgemeines Karten-Design */
.continent-facts-section .continent-facts-card {
    border: 2px solid #fff; /* Etwas dickerer weißer Rahmen */
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); /* Stärkerer Schatten */
    border-radius: 12px; /* Etwas größere Rundung */
    background-color: #fff;
    overflow: hidden; /* Verhindert Überlaufen */
    transition: transform 0.3s ease, box-shadow 0.3s ease; /* Für Hover-Effekte */
}

/* Hover-Effekt für Karten */
.continent-facts-section .continent-facts-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
}

/* Bild ohne Parallax und ohne Abdunkeln */
.continent-facts-section .bg-overlay {
    background-size: cover;
    background-position: center;
    height: 100%;
    position: relative;
    filter: saturate(1.5) contrast(1.2); /* Erhöhte Sättigung und Kontrast */
    transition: filter 0.3s ease; /* Übergang für den Filter */
}

/* Hover-Effekt für noch kräftigere Farben */
.continent-facts-section .continent-facts-card:hover .bg-overlay {
    filter: saturate(2.0) contrast(1.4); /* Noch kräftigere Farben beim Hover */
}

/* Content-Wrapper für Titel und Text */
.continent-facts-section .content-wrapper {
    position: absolute;
    bottom: 2rem; /* Am unteren Rand platzieren */
    left: 2rem;
    right: 2rem;
}

.continent-facts-section .card-text {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #fff;
    text-shadow: 0 3px 6px rgba(54, 52, 52, 0.8); /* Stärkerer Schatten für Lesbarkeit */
    margin-top: 1rem;
    background-color: rgba(80, 80, 80, 0.8); /* Transparenter grauer Hintergrund */
    padding: 0.5rem 1rem; /* Leichter Padding für den Hintergrund */
    border-radius: 6px; /* Abgerundete Ecken */
    display: inline-block; /* Passt sich an den Text an */
}

.continent-facts-section .placeholder-content {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #fff;
    text-shadow: 0 3px 6px rgba(0, 0, 0, 0.8);
}

.continent-facts-section .placeholder-content h4 {
    font-size: 2.5rem;
    font-weight: bold;
}

/* Titel-Styling */
.continent-facts-section .title-wrapper {
    background-color: rgba(255, 255, 255, 0.95);
    padding: 0.3rem 0.8rem;
    border-radius: 6px;
    display: inline-block;
    margin-bottom: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2); /* Leichter Schatten für den Titel */
}

.continent-facts-section .title-wrapper h4 {
    color: #0a0a0a; /* Dunklerer Text für Kontrast */
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    text-transform: uppercase;
    line-height: 1;
}

/* Faktencheck-Karte */
.continent-facts-section .card-header {
    background: linear-gradient(135deg, #4a90e2, #357abd); /* Gradient für den Header */
    color: #fff;
    padding: 1rem;
    border-radius: 10px 10px 0 0; /* Nur oben abgerundet */
}

.continent-facts-section .card-header h4 {
    font-size: 1.8rem;
    font-weight: 600;
}

/* Flagge mit Hover-Effekt */
.continent-facts-section .continent-flag {
    background: url('{{ asset('assets/flags/4x3/' . strtolower($country->country_code) . '.svg') }}') no-repeat center/cover;
    width: 100px;
    height: 75px;
    border: 3px solid #fff;
    border-radius: 8px;
    margin: -2.5rem auto 1.5rem;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease;
}

.continent-facts-section .continent-flag:hover {
    transform: scale(1.1);
}

/* Faktenelemente nebeneinander */
.continent-facts-section .fact-list {
    display: flex;
    flex-direction: column;
    gap: 1rem; /* Reduzierter Abstand zwischen den Reihen */
}

.continent-facts-section .fact-row {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.continent-facts-section .fact-row.full-width {
    display: block; /* Volle Breite für Visum und Reisewarnung */
}

.continent-facts-section .fact-item {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.5rem;
    transition: background-color 0.3s ease;
    min-width: 0; /* Verhindert Überlaufen */
}

.continent-facts-section .fact-item:hover {
    background-color: #f5f7fa;
    border-radius: 6px;
}

.continent-facts-section .fact-icon {
    font-size: 1.5rem;
    color: #4a90e2; /* Akzentfarbe */
    transition: color 0.3s ease;
}

.continent-facts-section .fact-item:hover .fact-icon {
    color: #357abd;
}

.continent-facts-section .fact-content {
    flex: 1;
}

.continent-facts-section .fact-label {
    color: #6c757d;
    font-size: 0.9rem;
    display: block;
}

.continent-facts-section .fact-content h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a1a1a;
}

/* Reisewarnung als Pipette */
.continent-facts-section .travel-warning-card {
    display: flex;
    align-items: center;
    gap: 0.75rem; /* Reduzierter Abstand */
    padding: 0.75rem;
    background: linear-gradient(135deg, #f5f7fa, #ebedf0); /* Neutraler Hintergrund */
    border-radius: 8px; /* Etwas kleinere Rundung */
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1); /* Leichter Schatten */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.continent-facts-section .travel-warning-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
}

/* Icon-Styling für Reisewarnung */
.continent-facts-section .travel-warning-card .fact-icon {
    font-size: 1.6rem; /* Etwas kleiner */
    color: #4a90e2;
    transition: transform 0.3s ease;
}

.continent-facts-section .travel-warning-card:hover .fact-icon {
    transform: rotate(10deg); /* Subtilere Rotation */
}

/* Warnungs-Content */
.continent-facts-section .travel-warning-content {
    flex: 1;
    color: #1a1a1a;
}

.continent-facts-section .travel-alert {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

/* Pipette-Design */
/* Pipette-Design */
.continent-facts-section .pipette-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.continent-facts-section .pipette {
    position: relative;
    width: 20px; /* Schlankere Pipette */
    height: 80px; /* Etwas kleinere Höhe */
    /* Adjusted gradient to emphasize green at the bottom (Level 1) */
    background: linear-gradient(to bottom, #e74c3c 0%, #e67e22 25%, #f1c40f 50%, #2ecc71 75%, #27ae60 100%); /* More green at the bottom */
    border-radius: 10px; /* Etwas kleinere Rundung */
    border: 2px solid #fff;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2), 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

/* Remove the pipette-fill styling since it's no longer needed */
.continent-facts-section .pipette-fill {
    display: none; /* Not needed anymore */
}

/* Markierungen innerhalb der Pipette */
.continent-facts-section .pipette-marker {
    position: absolute;
    width: 4px;
    height: 4px;
    background-color: rgba(255, 255, 255, 0.5); /* Subtile Markierungen */
    border-radius: 50%;
    left: 50%;
    transform: translateX(-50%);
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.continent-facts-section .pipette-marker.level-1 { top: 75%; } /* 25% von unten */
.continent-facts-section .pipette-marker.level-2 { top: 50%; } /* 50% von unten */
.continent-facts-section .pipette-marker.level-3 { top: 25%; } /* 75% von unten */
.continent-facts-section .pipette-marker.level-4 { top: 5%; }  /* 95% von unten */

/* Update marker colors to match the new gradient */
.continent-facts-section .travel-warning-card.no-warning .pipette-marker.level-1,
.continent-facts-section .travel-warning-card.Level-1 .pipette-marker.level-1 {
    background-color: #27ae60; /* Darker green for Level 1 */
    transform: translateX(-50%) scale(1.5);
}
.continent-facts-section .travel-warning-card.Level-2 .pipette-marker.level-2 {
    background-color: #f1c40f; /* Gelb für Level 2 */
    transform: translateX(-50%) scale(1.5);
}
.continent-facts-section .travel-warning-card.Level-3 .pipette-marker.level-3 {
    background-color: #e67e22; /* Orange für Level 3 */
    transform: translateX(-50%) scale(1.5);
}
.continent-facts-section .travel-warning-card.Level-4 .pipette-marker.level-4 {
    background-color: #e74c3c; /* Rot für Level 4 */
    transform: translateX(-50%) scale(1.5);
}

/* Pfeil */
.continent-facts-section .pipette-arrow {
    position: absolute;
    left: 20px; /* Näher an der Pipette */
    color: #333;
    font-size: 1rem; /* Kleinerer Pfeil */
    animation: pulse-arrow 1.5s infinite ease-in-out; /* Sanftere Animation */
}

/* Animation für den Pfeil */
@keyframes pulse-arrow {
    0% { transform: translateX(0); }
    50% { transform: translateX(3px); } /* Subtilere Bewegung */
    100% { transform: translateX(0); }
}

/* Pfeilposition basierend auf Warnstufe (unchanged) */
.continent-facts-section .travel-warning-card.no-warning .pipette-arrow {
    top: 65px; /* Pfeil unten (Grün) */
}

.continent-facts-section .travel-warning-card.Level-1 .pipette-arrow {
    top: 54px; /* Pfeil auf Level 1 */
}

.continent-facts-section .travel-warning-card.Level-2 .pipette-arrow {
    top: 38px; /* Pfeil auf Level 2 */
}

.continent-facts-section .travel-warning-card.Level-3 .pipette-arrow {
    top: 22px; /* Pfeil auf Level 3 */
}

.continent-facts-section .travel-warning-card.Level-4 .pipette-arrow {
    top: 6px; /* Pfeil auf Level 4 */
}

.continent-facts-section .travel-warning-text {
    font-size: 0.95rem; /* Etwas kleiner für Balance */
    line-height: 1.3;
    margin-left: 0.5rem; /* Leichter Abstand */
}

.continent-facts-section .travel-warning-text small {
    font-size: 0.75rem;
    opacity: 0.8;
}

/* Synchronisierte Höhe der Karten */
.continent-facts-section .card-equal-height {
    height: 100%;
}

@media (min-width: 992px) {
    .continent-facts-section .card-equal-height {
        min-height: 320px; /* Etwas kleinere Mindesthöhe */
    }
}

@media (max-width: 991px) {
    .continent-facts-section {
        padding: 2rem 0;
    }
    .continent-facts-section .content-wrapper {
        bottom: 1rem;
        left: 1rem;
        right: 1rem;
    }
    .continent-facts-section .card-text { font-size: 1rem; }
    .continent-facts-section .title-wrapper h4 { font-size: 1.75rem; }
    .continent-facts-section .continent-flag { width: 80px; height: 60px; margin: -2rem auto 1rem; }
    .continent-facts-section .fact-row { flex-direction: column; gap: 0.5rem; }
    .continent-facts-section .fact-item { gap: 0.75rem; }
    .continent-facts-section .fact-icon { font-size: 1.2rem; }
    .continent-facts-section .fact-content h5 { font-size: 1rem; }
    .continent-facts-section .card-equal-height {
        height: auto;
    }
    .continent-facts-section .travel-warning-card {
        padding: 0.6rem;
    }
    .continent-facts-section .travel-warning-card .fact-icon {
        font-size: 1.4rem;
    }
    .continent-facts-section .travel-warning-text {
        font-size: 0.85rem;
    }
    .continent-facts-section .travel-warning-text small {
        font-size: 0.7rem;
    }
    .continent-facts-section .pipette-arrow {
        font-size: 0.9rem;
        left: 22px;
    }
}


</style>
