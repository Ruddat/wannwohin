<section class="continent-section bg-color-light m-0 pb-0">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <!-- Continent Text -->
            <div class="col-12 col-lg-7">
                <div class="continent-card card h-100">
                    <div class="card-body p-4">
                        <h5 class="card-title text-uppercase text-primary">
                            @autotranslate($continent->title, app()->getLocale()) @autotranslate("Facts", app()->getLocale())
                        </h5>
                        <p class="card-text">
                            @autotranslate($continent->continent_text, app()->getLocale())
                        </p>
                    </div>
                </div>
            </div>

            <!-- Continent Info Card -->
            <div class="col-12 col-lg-5">
                <div class="continent-info-card card h-100">
                    <div class="card-header text-center p-4">
                        <h4 class="text-uppercase mb-0">@autotranslate($continent->title, app()->getLocale())</h4>
                    </div>
                    <div class="card-body bg-white pt-4">
                        <div class="continent-flag mb-3 mx-auto"
                             style="background-image: url('{{ asset("assets/img/location_main_img/{$continent->alias}.png") }}');">
                        </div>
                        <table class="continent-table table table-borderless table-sm">
                            <tr>
                                <td>
                                    <span class="text-muted">@autotranslate("Area", app()->getLocale()) (km²)</span>
                                    <h5>{{ number_format($continent->area_km) }}</h5>
                                </td>
                                <td>
                                    <span class="text-muted">@autotranslate('Population', app()->getLocale())</span>
                                    <h5>{{ number_format($continent->population) }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <span class="text-muted">@autotranslate('Countries', app()->getLocale())</span>
                                    <h5>{{ $continent->no_countries }}</h5>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style scoped>
.continent-section {
    background-color: #eaeff5;
}

.continent-section .continent-card,
.continent-section .continent-info-card {
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    background-color: #fff;
}

.continent-section .continent-card .card-title {
    font-size: 1.25rem;
    font-weight: bold;
    color: #007bff;
}

.continent-section .continent-info-card .card-header {
    background-color: #dbdbdb;
    padding: 16px;
}

.continent-section .continent-info-card .card-body {
    padding: 16px;
}

.continent-section .continent-flag {
    background-size: cover;
    background-position: center;
    width: 100px;
    height: 100px;
    border: 2px solid #ddd;
    border-radius: 50%;
    margin: -50px auto 20px;
}

.continent-section .continent-table td {
    text-align: center;
    padding: 8px;
}

.continent-section .continent-table .text-muted {
    color: #6c757d;
    font-size: 0.9rem;
}

.continent-section .continent-table h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: bold;
}

@media (max-width: 768px) {
    .continent-section .continent-card,
    .continent-section .continent-info-card {
        text-align: center;
    }

    .continent-section .continent-card .card-text,
    .continent-section .continent-info-card .card-body {
        font-size: 0.9rem;
    }

    .continent-section .continent-table td {
        padding: 6px;
    }
}
</style>
