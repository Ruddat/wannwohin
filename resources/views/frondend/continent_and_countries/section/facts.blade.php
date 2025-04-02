<section class="continent-facts-section section m-0 pb-0">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <!-- Continent Text -->
            <div class="col-12 col-lg-7" data-aos="fade-right" data-aos-duration="500">
                <div class="continent-facts-card card position-relative card-equal-height">
                    <div class="card-body p-4 bg-overlay" style="background-image: url('{{ Storage::url($continent->image1_path ?? $continent->image2_path ?? "assets/img/location_main_img/{$continent->alias}.png") }}');">
                        <div class="content-wrapper">
                            <div class="title-wrapper">
                                <h4 class="text-uppercase text-dark m-0">
                                    @autotranslate($continent->title, app()->getLocale()) @autotranslate("Facts", app()->getLocale())
                                </h4>
                            </div>
                            @if (!empty($continent->continent_text))
                                <div class="card-text text-white">
                                    @autotranslate($continent->continent_text, app()->getLocale())
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Continent Info Card -->
            <div class="col-12 col-lg-5" data-aos="fade-left" data-aos-duration="500">
                <div class="continent-facts-card card card-equal-height">
                    <div class="card-header text-center p-3">
                        <h4 class="text-uppercase mb-0">
                            @autotranslate($continent->title, app()->getLocale())
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        @if ($continent->fact_card_image)
                        <div class="continent-flag mx-auto mb-4" style="background-image: url('{{ Storage::url($continent->fact_card_image) }}');"></div>
                    @else
                        <div class="continent-flag mx-auto mb-4" style="background-image: url('{{ asset("assets/img/location_main_img/{$continent->alias}.png") }}');"></div>
                    @endif

                        <div class="fact-list">
                            <div class="fact-row">
                                <div class="fact-item">
                                    <span class="fact-icon"><i class="fas fa-ruler-combined"></i></span>
                                    <div class="fact-content">
                                        <span class="fact-label">@autotranslate('Area (km²)', app()->getLocale())</span>
                                        <h5>{{ number_format($continent->area_km) }}</h5>
                                    </div>
                                </div>
                                <div class="fact-item">
                                    <span class="fact-icon"><i class="fas fa-users"></i></span>
                                    <div class="fact-content">
                                        <span class="fact-label">@autotranslate('Population', app()->getLocale())</span>
                                        <h5>{{ number_format($continent->population) }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="fact-row full-width centered">
                                <div class="fact-item">
                                    <span class="fact-icon"><i class="fas fa-globe"></i></span>
                                    <div class="fact-content">
                                        <span class="fact-label">@autotranslate('Countries', app()->getLocale())</span>
                                        <h5>{{ $continent->no_countries }}</h5>
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
    background-color: #f5f7fa;
    padding: 4rem 0;
}

.continent-facts-section .continent-facts-card {
    border: 2px solid #fff;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    border-radius: 12px;
    background-color: #fff;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.continent-facts-section .continent-facts-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
}

.continent-facts-section .bg-overlay {
    background-size: cover;
    background-position: center;
    height: 100%;
    position: relative;
    filter: saturate(1.5) contrast(1.2);
    transition: filter 0.3s ease;
}

.continent-facts-section .continent-facts-card:hover .bg-overlay {
    filter: saturate(2.0) contrast(1.4);
}

.continent-facts-section .content-wrapper {
    position: absolute;
    bottom: 2rem;
    left: 2rem;
    right: 2rem;
}

.continent-facts-section .card-text {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #fff;
    text-shadow: 0 3px 6px rgba(0, 0, 0, 0.8);
    margin-top: 1rem;
    background-color: rgba(128, 128, 128, 0.5);
    padding: 0.5rem 1rem;
    border-radius: 6px;
    display: inline-block;
}

.continent-facts-section .title-wrapper {
    background-color: rgba(255, 255, 255, 0.95);
    padding: 0.3rem 0.8rem;
    border-radius: 6px;
    display: inline-block;
    margin-bottom: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.continent-facts-section .title-wrapper h4 {
    color: #1a1a1a;
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    text-transform: uppercase;
    line-height: 1;
}

.continent-facts-section .card-header {
    background: linear-gradient(135deg, #4a90e2, #357abd);
    color: #fff;
    padding: 1rem;
    border-radius: 10px 10px 0 0;
}

.continent-facts-section .card-header h4 {
    font-size: 1.8rem;
    font-weight: 600;
}

.continent-facts-section .continent-flag {
    background-size: cover;
    background-position: center;
    width: 100px;
    height: 100px;
    border: 2px solid #ddd;
    border-radius: 50%;
    margin: -50px auto 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease;
}

.continent-facts-section .continent-flag:hover {
    transform: scale(1.1);
}

.continent-facts-section .fact-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.continent-facts-section .fact-row {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.continent-facts-section .fact-row.full-width {
    display: flex; /* Änderung von 'block' zu 'flex', um Zentrierung zu ermöglichen */
}

.continent-facts-section .fact-row.centered {
    justify-content: center; /* Zentriert die fact-item-Box horizontal */
}

.continent-facts-section .fact-item {
    display: flex; /* Flexbox für Icon und Text */
    align-items: center; /* Vertikale Zentrierung */
    gap: 1rem; /* Abstand zwischen Icon und Text */
    padding: 0.5rem;
    transition: background-color 0.3s ease;
    min-width: 0;
    flex: 1; /* Standardverhalten für nicht-zentrierte Items */
}

.continent-facts-section .fact-row.centered .fact-item {
    flex: 0 1 auto; /* Verhindert volle Breite für zentrierte Items */
}

.continent-facts-section .fact-item:hover {
    background-color: #f5f7fa;
    border-radius: 6px;
}

.continent-facts-section .fact-icon {
    font-size: 1.5rem;
    color: #4a90e2;
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

.continent-facts-section .card-equal-height {
    height: 100%;
}

@media (min-width: 992px) {
    .continent-facts-section .card-equal-height {
        min-height: 320px;
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
}
</style>
