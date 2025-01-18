<section class="custom-header-section section section-no-border section-parallax bg-transparent custom-section-padding-1 custom-position-1 custom-xs-bg-size-cover parallax-no-overflow m-0" data-plugin-parallax data-plugin-options="{'speed': 1.5}" data-image-src="{{ $panoramaLocationPicture ? url($panoramaLocationPicture) : asset('default-main.jpg') }}">
    <div class="container">
        <div class="row">
            <!-- Hauptbild -->
            <div class="custom-header-img col-lg-4 position-relative custom-sm-margin-bottom-1">
                <img src="{{ $mainLocationPicture ? url($mainLocationPicture) : asset('default-main.jpg') }}" class="img-fluid custom-border custom-image-position-2 custom-box-shadow-4" alt="Main Image" />
            </div>
            <!-- Header-Text -->
            <div class="col-lg-6 col-xl-5">
                <span class="custom-header-text">
                    {!! app('autotranslate')->trans($panoramaLocationText ?? '<h1>Default Header Text</h1>', app()->getLocale()) !!}
                </span>

                <!-- Admin-Button nur sichtbar für eingeloggte Admins -->
                @if(Auth::guard('admin')->check())
                    <a href="{{ route('verwaltung.header-manager.header_contents.index') }}" target="_blank" class="btn btn-primary mt-3">
                        Bearbeiten
                    </a>
                @endif
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
        <div class="custom-row"> <!-- Benutzerdefinierte Klasse -->
            {{-- Kontinent-Dropdown --}}
            <div class="custom-dropdown">
                @livewire('frontend.continent-selector.continent-selector-component')
            </div>

            {{-- Reiseziele-Link --}}
            <div class="custom-suggestion">
                @livewire('frontend.header-search.header-search-component')
            </div>

            {{-- Location-Suggestion
            <div class="custom-suggestion">
                @livewire('frontend.location-suggestion.location-suggestion-component')
            </div>
            --}}
        </div>
    </div>
</div>
<x-breadcrumb />
<hr class="custom-suggestion">

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

@media (min-width: 991px) {
    .custom-image-position-2 {
        position: absolute;
        max-width: 90%;
        top: -60px;
        z-index: 1;
        height: 400px;
    }
    .custom-section-padding-1 {
        padding-top: 200px !important;
        padding-bottom: 70px !important;
    }
}

/* Hauptcontainer für die Links */
.custom-about-links {
    background-color: #f9f9f9; /* Hintergrundfarbe */
    padding: 10px 0; /* Innenabstand oben und unten */
}

/* Zeile für die Elemente */
.custom-about-links .custom-row {
    display: flex;
    justify-content: flex-end; /* Elemente rechts ausrichten */
    align-items: baseline;
    gap: 15px; /* Abstand zwischen den Elementen */
}

/* Stil für das Kontinent-Dropdown */
.custom-about-links .custom-dropdown {
    flex: 0 0 auto; /* Automatische Breite basierend auf dem Inhalt */
}

/* Stil für den Reiseziele-Link */
/* Hauptcontainer für die Links */
.custom-about-links {
    background-color: #f9f9f9; /* Hintergrundfarbe */
    padding: 10px 0; /* Innenabstand oben und unten */
}

/* Zeile für die Elemente */
.custom-about-links .custom-row {
    display: flex;
    justify-content: flex-end; /* Elemente rechts ausrichten */
    align-items: baseline; /* Elemente an der Grundlinie ausrichten */
    gap: 15px; /* Abstand zwischen den Elementen */
}


/* Stil für das Kontinent-Dropdown */
.custom-about-links .custom-dropdown {
    flex: 0 0 auto; /* Automatische Breite basierend auf dem Inhalt */
    position: relative; /* Für das Pfeil-Symbol */
}

.custom-about-links .custom-dropdown select {
    width: 100%; /* Volle Breite des Containers */
    padding: 8px 32px 8px 12px; /* Innenabstand: oben, rechts, unten, links */
    font-size: 14px; /* Schriftgröße */
    border: 1px solid #ccc; /* Rahmen */
    border-radius: 4px; /* Abgerundete Ecken */
    background-color: #FDD55C; /* Hintergrundfarbe */
    appearance: none; /* Standard-Pfeil entfernen */
    -webkit-appearance: none; /* Für Safari */
    -moz-appearance: none; /* Für Firefox */
}


/* Responsive Anpassungen */
@media (max-width: 768px) {
    .custom-about-links .custom-dropdown {
        width: 100%; /* Volle Breite auf kleinen Bildschirmen */
    }

    .custom-about-links .custom-dropdown select {
        padding: 8px 32px 8px 12px; /* Innenabstand beibehalten */
    }
}

/* Stil für den Reiseziele-Link */
.custom-about-links .custom-link {
    flex: 0 0 auto;
    text-align: center;
    padding: 8px 12px; /* Innenabstand */
}

.custom-about-links .custom-link a {
    text-decoration: none; /* Unterstrich entfernen */
    color: #333; /* Textfarbe */
}

.custom-about-links .custom-link a:hover {
    color: #007bff; /* Textfarbe beim Hover */
}

/* Stil für den Vorschlag-Button */
.custom-about-links .custom-suggestion {
    flex: 0 0 auto;
    text-align: center;
    padding: 8px 12px;
}

.custom-about-links .custom-suggestion a {
    text-decoration: none;
    color: #333;
}

.custom-about-links .custom-suggestion a:hover {
    color: #007bff;
}

/* Responsive Anpassungen */
@media (max-width: 768px) {
    .custom-about-links .custom-row {
        flex-direction: column; /* Elemente untereinander anzeigen */
        align-items: center; /* Zentrieren statt rechtsbündig */
        gap: 10px; /* Abstand zwischen den Elementen */
    }

    .custom-about-links .custom-link,
    .custom-about-links .custom-suggestion,
    .custom-about-links .custom-dropdown {
        width: 100%; /* Volle Breite auf kleinen Bildschirmen */
        text-align: center; /* Text zentrieren */
        padding: 8px 0; /* Innenabstand anpassen */
    }

    .custom-about-links .custom-link {
        border-bottom: none; /* Rahmenlinie entfernen */
    }
}

</style>
