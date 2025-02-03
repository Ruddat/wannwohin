<section id="experience" class="section section-secondary section-no-border mt-5 mb-5 pt-0">
    <div class="container">
        <div class="row g-4">
            @foreach($countries as $index => $country)
            <div class="col-12 col-sm-6 col-md-6 col-lg-4" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="{{ $index * 100 }}">
                @php
                    $location = $country->locations()->first();
                    $primaryImage = $country?->primaryImage() ?? asset('img/default-location.png');
                @endphp

                <a href="{{ route('list-country-locations', ['continentAlias' => $continent->alias, 'countryAlias' => $country->alias]) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-lg custom-card" style="background-image: url('{{ $primaryImage }}');">
                        <div class="card-body d-flex align-items-end">
                            <div class="bg-opacity-75 bg-white rounded text-dark p-3">
                                <h4 class="m-0 text-center">{{ $country->title }}</h4>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Bearbeiten-Button nur für Admins -->
                @if(Auth::guard('admin')->check())
                <div class="mt-2 text-center">
                    <a href="{{ route('country-manager.edit', $country->id) }}" target="_blank" class="btn btn-sm btn-warning">
                        <i class="ti ti-edit"></i> Admin eingeloggt! Bearbeiten
                    </a>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- AOS CSS & JS einbinden -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        AOS.init({
            duration: 1000,  // Standard Animationsdauer
            once: true       // Animation nur einmal pro Scrollvorgang
        });
    });
</script>

<style>
#experience .card {
    height: 350px; /* Deutlich höhere Karten */
    border-radius: 12px; /* Abgerundete Ecken */
    overflow: hidden; /* Schutz vor Überlauf */
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    background-size: cover; /* Vollflächiges Bild */
    background-position: center; /* Zentriertes Bild */
    transition: transform 0.3s ease; /* Animation beim Hover */
}

#experience .card:hover {
    transform: scale(1.05); /* Vergrößerung beim Hover */
}

#experience .card .card-body {
    padding: 0;
}

#experience .bg-opacity-75 {
    width: fit-content;
    max-width: 100%;
    text-align: center;
    font-size: 1.2rem; /* Größerer Text */
}

#experience .card h4 {
    font-size: 1.5rem; /* Größere Schriftgröße */
    font-weight: bold;
}

@media (max-width: 992px) {
    #experience .card {
        height: 500px; /* Reduzierte Höhe auf mittleren Geräten */
    }
}

@media (max-width: 768px) {
    #experience .card {
        height: 400px; /* Reduzierte Höhe auf kleinen Geräten */
    }
}



</style>
