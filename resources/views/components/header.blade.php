<section class="custom-header-section section section-no-border section-parallax bg-transparent custom-section-padding-1 custom-position-1 custom-xs-bg-size-cover parallax-no-overflow m-0" data-plugin-parallax data-plugin-options="{'speed': 1.5}" data-image-src="{{ $panoramaLocationPicture ?? asset('default-bg.jpg') }}">
    <div class="container">
        <div class="row">
            <!-- Hauptbild -->
            <div class="col-lg-4 position-relative custom-sm-margin-bottom-1">
                <img src="{{ $mainLocationPicture ?? asset('default-main.jpg') }}" class="img-fluid custom-border custom-image-position-2 custom-box-shadow-4" alt="Main Image" />
            </div>
            <!-- Header-Text -->
            <div class="col-lg-6 col-xl-5">
                <span class="custom-header-text">{!! $panoramaLocationText ?? '<h1>Default Header Text</h1>' !!}</span>
            </div>
            <!-- Scroll-Icon -->
            <div class="col-lg-2 col-xl-3 d-none d-lg-block">
                <img src="{{ asset('assets/img/pages/main/mouse.png') }}" class="img-fluid custom-image-pos-1" alt="Scroll Icon" />
            </div>
        </div>
    </div>
</section>

<div class="custom-about-links bg-color-light">
    <div class="container">
        <div class="row justify-content-end">
            <!-- Kontinent-Dropdown -->
            <div class="col-lg-3 text-center custom-xs-border-bottom p-0">
                <form action="#" method="GET">
                    <select name="continent_id" class="form-select">
                        <option value="" selected>Wähle einen Kontinent</option>
                        <option value="1">Europa</option>
                        <option value="2">Asien</option>
                        <option value="3">Afrika</option>
                        <option value="4">Nordamerika</option>
                        <option value="5">Südamerika</option>
                        <option value="6">Australien</option>
                        <option value="7">Antarktis</option>
                    </select>
                </form>
            </div>

            <div class="col-lg-2 text-center custom-xs-border-bottom p-0">
                <a data-hash href="#say-hello" class="text-decoration-none">
                    <span class="custom-nav-button custom-divisors text-color-dark">
                        <i class="icon-envelope-open icons text-color-primary"></i> Reiseziele im
                    </span>
                </a>
            </div>

            <div class="col-lg-3 text-center p-0">
                <a href="#" class="text-decoration-none">
                    <span class="custom-nav-button text-color-dark">
                        <i class="icon-cloud-download icons text-color-primary"></i> Neuen Ort vorschlagen
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>


<style>
/* Spezifisches Styling nur für die Header-Sektion */
.custom-header-section {
    position: relative;
    overflow: hidden;
}

/* Styling für den Text */
.custom-header-section .custom-header-text {
    color: #fff; /* Weiße Schrift */
    font-size: 1.5rem; /* Schriftgröße */
    font-weight: bold; /* Fettgedruckt */
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7); /* Textschatten */
    background: rgba(0, 0, 0, 0.5); /* Halbtransparenter Hintergrund */
    padding: 15px; /* Innenabstand */
    border-radius: 8px; /* Abgerundete Ecken */
    display: inline-block; /* Block für den Hintergrund */
}

/* Styling für Bilder */
.custom-header-section img {
    border-radius: 10px; /* Abgerundete Ecken */
}

/* Animation für Scroll-Icon */
.custom-header-section .custom-image-pos-1 {
    animation: bounce 2s infinite; /* Springen-Animation */
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.custom-about-links select.form-select {
    background-color: #FDD55C;
}
.custom-about-links .form-select {
    width: 100%;
    padding: 0.5rem;
    margin-top: 1.5rem;
}


/* Responsives Design */
@media (max-width: 768px) {
    .custom-header-section .custom-header-text {
        font-size: 1.2rem; /* Kleinere Schriftgröße */
        padding: 10px; /* Weniger Innenabstand */
    }
}
</style>
