<div class="modal fade" id="cookie-modal" tabindex="-1" aria-labelledby="cookieModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content shadow-xl border-0" style="background: linear-gradient(135deg, #ffffff, #eef2f6);">
            <!-- Header -->
            <div class="modal-header border-0 pb-1" style="background: linear-gradient(90deg, #007bff, #00c4ff);">
                <h5 class="modal-title font-weight-bold text-white" id="cookieModalLabel">
                    <i class="fas fa-cookie-bite me-2"></i> Deine Cookie-Wahl
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Body -->
            <div class="modal-body pt-4">
                <p class="text-muted mb-4 lead" style="line-height: 1.5;">Wir möchten deine Reise auf unserer Seite so angenehm wie möglich gestalten. Dafür setzen wir Cookies ein – du bestimmst, welche!</p>
                <!-- Einstellungen -->
                <div id="cookie-settings" class="collapse mb-4">
                    <div class="card border-0 shadow-sm mb-3 p-3 bg-light">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="essential-cookies" checked disabled>
                            <label class="form-check-label fw-bold text-dark" for="essential-cookies">
                                <i class="fas fa-lock me-1 text-muted"></i> Essenziell
                            </label>
                            <small class="text-muted d-block ms-4">Notwendig für die Grundfunktionen der Seite.</small>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="analytics-cookies">
                            <label class="form-check-label fw-bold text-dark" for="analytics-cookies">
                                <i class="fas fa-chart-pie me-1 text-primary"></i> Statistiken
                            </label>
                            <small class="text-muted d-block ms-4">Hilft uns, die Seite zu optimieren (z. B. Analytics).</small>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="maps-cookies">
                            <label class="form-check-label fw-bold text-dark" for="maps-cookies">
                                <i class="fas fa-map-marked-alt me-1 text-success"></i> Karten
                            </label>
                            <small class="text-muted d-block ms-4">Für interaktive Karten (z. B. Google Maps).</small>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-link text-primary fw-bold p-0 mb-3" data-bs-toggle="collapse" data-bs-target="#cookie-settings">
                    <i class="fas fa-cog me-1"></i> Einstellungen anpassen
                </button>
            </div>
            <!-- Footer -->
            <div class="modal-footer border-0 pt-0">
                <button type="button" id="decline-cookies" class="btn btn-outline-danger btn-sm px-4 py-2" data-bs-dismiss="modal">Ablehnen</button>
                <button type="button" id="save-cookies" class="btn btn-outline-primary btn-sm px-4 py-2 me-2">Speichern</button>
                <button type="button" id="accept-cookies" class="btn btn-success btn-sm px-4 py-2">Alle akzeptieren</button>
            </div>
        </div>
    </div>
</div>
