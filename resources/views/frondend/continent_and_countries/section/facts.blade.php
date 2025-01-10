<section class="continent-section section-no-border bg-color-light m-0 pb-0">
    <div class="container">
        <div class="row g-4 align-items-center">
            <!-- Continent Text -->
            <div class="col-12 col-lg-7">
                <div class="card h-100 custom-border continent-card">
                    <div class="card-body p-4">
                        <h5 class="card-title text-uppercase text-primary">@autotranslate("About", app()->getLocale()) @autotranslate($continent->title, app()->getLocale())</h5>
                        <p class="card-text">
                            @autotranslate($continent->continent_text, app()->getLocale())
                        </p>
                    </div>
                </div>
            </div>

            <!-- Continent Info Card -->
            <div class="col-12 col-lg-5">
                <div class="card h-100 continent-info-card">
                    <div class="card-header text-center p-4">
                        <h4 class="text-uppercase mb-0">@autotranslate($continent->title, app()->getLocale())</h4>
                    </div>
                    <div class="card-body bg-white pt-4">
                        <div class="text-center mb-3">
                            <div class="continent-flag"
                                style="background-image : url('{{ asset("assets/img/location_main_img/{$continent->alias}.png") }}')">
                            </div>
                        </div>
                        <table class="table table-borderless table-sm text-center">
                            <tr>
                                <td>
                                    <span class="text-muted">Area (km²)</span>
                                    <div><h5 class="m-0">{{ number_format($continent->area_km) }}</h5></div>
                                </td>
                                <td>
                                    <span class="text-muted">Population</span>
                                    <div><h5 class="m-0">{{ number_format($continent->population) }}</h5></div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <span class="text-muted">Countries</span>
                                    <div><h5 class="m-0">{{ $continent->no_countries }}</h5></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Container Style */
.continent-section {
    background-color: #eaeff5 !important;
}

/* Karten-Styles */
.continent-card,
.continent-info-card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    background-color: #fff;
}

.continent-card .card-title {
    font-size: 1.25rem;
    font-weight: bold;
    color: #007bff; /* Primärfarbe für Titel */
}

.continent-info-card .card-header {
    background-color: #dbdbdb !important;
    text-align: center;
    padding: 16px;
}

.continent-info-card .card-body {
    padding: 16px;
}

/* Flaggen-Styles */
.continent-flag {
    background-size: cover;
    background-position: center;
    width: 100px;
    height: 100px;
    border: 2px solid #ddd;
    border-radius: 50%;
    margin: -50px auto;
}

/* Tabelle-Styles */
.table-borderless {
    margin-top: 20px;
}

.table-borderless td {
    text-align: center;
}

.table-borderless span {
    color: #6c757d; /* Muted Text */
}

.table-borderless h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: bold;
}

/* Responsivität */
@media (max-width: 768px) {
    .continent-section .col-lg-7,
    .continent-section .col-lg-5 {
        text-align: center;
    }

    .continent-card .card-body p,
    .continent-info-card .card-body p {
        font-size: 0.9rem;
    }
}
</style>
