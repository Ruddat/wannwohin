<section class="section section-no-border bg-color-light m-0 pb-0" style="background-color: #eaeff5 !important;">
    <div class="container">
        <h1 class="text-center mb-4">@autotranslate("Locations in", app()->getLocale()) {{ $country->title }}</h1>
        <div class="row g-4">
            @foreach($locations as $location)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3" data-aos="fade-up">
                    <div class="card h-100 border-0 shadow-sm custom-card position-relative">
                        <!-- Bearbeiten-Button (nur für Admins sichtbar) -->
                        @if(Auth::guard('admin')->check())
                        <!-- Debug-Ausgabe -->
                        <a href="{{ route('location-manager.edit', $location->id) }}" target="_blank" class="btn btn-sm btn-warning position-absolute top-0 end-40 m-2 card-edit-button">
                            <i class="ti ti-edit"></i> @autotranslate('Bearbeiten', app()->getLocale())
                        </a>
                    @endif
    <!-- Wishlist-Button oben rechts als Overlay -->
    <div class="wishlist-overlay1">
        @livewire('frontend.wishlist-select.wishlist-button-component', ['locationId' => $location->id])
    </div>
                        <!-- Bild -->
                        <a href="{{ route('location.details', [
                            'continent' => $location->country->continent->alias,
                            'country' => $location->country->alias,
                            'location' => $location->alias,
                        ]) }}" class="text-decoration-none">
                            <div class="card-img-wrapper">
                                <img src="{{ $location->primaryImage() }}" class="card-img-top" alt="@autotranslate($location->title, app()->getLocale())">
                            </div>
                        </a>

                        <!-- Inhalt -->
                        <div class="card-body d-flex flex-column">
                            <!-- Titel -->
                            <h5 class="card-title text-truncate text-center"> {{ $location->title }}</h5>
                            <!-- Text -->
                            <p class="card-text">
                                @if (!empty($location->text_short))
                                    @autotranslate(Str::limit(strip_tags($location->text_short), 150), app()->getLocale())
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="mb-6"></div>
        </div>
    </div>
</section>




<style>
/* Allgemeine Karte */
.custom-card {
    border-radius: 12px; /* Abgerundete Ecken */
    overflow: hidden; /* Zusätzliche Sicherheit */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    padding-bottom: 1rem; /* Platz nach unten */
}

.custom-card:hover {
    transform: translateY(-5px); /* Heben beim Hover */
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2); /* Schatten */
}

/* Bildbereich */
.card-img-wrapper {
    height: 200px; /* Feste Bildhöhe */
    overflow: hidden;
    border-bottom: 2px solid #ddd; /* Trennlinie */
}

.card-img-top {
    height: 100%;
    width: 100%;
    object-fit: cover; /* Bild vollständig anzeigen */
    transition: transform 0.3s ease; /* Zoom-Effekt */
}

.custom-card:hover .card-img-top {
    transform: scale(1.1); /* Zoom beim Hover */
}

/* Karte: Body */
.card-body {
    flex-grow: 1; /* Platz nutzen */
    padding: 1rem; /* Innenabstand für Text */
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* Platzierung von Titel und Text */
}

/* Titel und Text */
.card-title {
    font-size: 1.1rem;
    font-weight: bold;
    color: #333; /* Standardfarbe */
    margin-bottom: 0.5rem;
}

.card-text {
    color: #555; /* Dezente Textfarbe */
    overflow: hidden;
    text-overflow: ellipsis; /* Fügt "..." hinzu */
    display: -webkit-box;
    -webkit-line-clamp: 3; /* Max. Zeilen */
    -webkit-box-orient: vertical;
    height: auto; /* Automatische Höhe */
    font-size: 0.9rem;
    margin-bottom: 1rem; /* Abstand zum Footer */
}

/* Hover-Text und Link */
.custom-card a {
    color: inherit; /* Keine Änderung der Textfarbe */
}

.custom-card a:hover {
    text-decoration: none;
    color: #007bff; /* Farbe beim Hover */
}

/* Bearbeiten-Button mit Hover-Effekt */
.card-edit-button {
    transition: opacity 0.3s ease;
    z-index: 10; /* Im Vordergrund */
    width: fit-content;
}

.custom-card:hover .card-edit-button {
    opacity: 3;
}

/* Responsiv */
@media (max-width: 768px) {
    .card-img-wrapper {
        height: 150px; /* Kleinere Höhe für mobile Geräte */
    }

    .card-title {
        font-size: 1rem; /* Kleinere Schriftgröße */
    }

    .card-text {
        -webkit-line-clamp: 2; /* Weniger Zeilen */
    }
}




/* Wishlist-Overlay oben rechts */
.wishlist-overlay1 {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
}


</style>
