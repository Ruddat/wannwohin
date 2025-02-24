<section id="experience" class="experience-section section section-secondary mt-5 mb-5 pt-0">
    <div class="container">
        <div class="row g-4">
            @foreach($countries as $index => $country)
                <div class="col-12 col-sm-6 col-md-6 col-lg-4 experience-item"
                     data-aos="fade-up"
                     data-aos-duration="400"
                     data-aos-delay="{{ $index * 50 }}">
                    @php
                        $primaryImage = $country->primaryImage() ?? asset('img/default-location.png');
                    @endphp

                    <a href="{{ route('list-country-locations', ['continentAlias' => $continent->alias, 'countryAlias' => $country->alias]) }}"
                       class="text-decoration-none">
                        <div class="experience-card card border-0 shadow-lg">
                            <div class="card-img-wrapper" style="background-image: url('{{ $primaryImage }}');">
                                <div class="card-overlay"></div>
                            </div>
                            <div class="card-body d-flex align-items-end">
                                <div class="card-title-wrapper bg-opacity-75 bg-white rounded text-dark p-3">
                                    <h4 class="m-0 text-center">{{ $country->title }}</h4>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Bearbeiten-Button nur für Admins -->
                    @auth('admin')
                        <div class="mt-2 text-center">
                            <a href="{{ route('country-manager.edit', $country->id) }}"
                               target="_blank"
                               class="btn btn-sm btn-warning">
                                <i class="ti ti-edit"></i> Bearbeiten
                            </a>
                        </div>
                    @endauth
                </div>
            @endforeach
        </div>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    AOS.init({
        duration: 400,  // Schnellere Animation
        delay: 0,      // Basis-Delay (individuell via data-aos-delay)
        once: true     // Nur einmal animieren
    });
});
</script>

<style scoped>
.experience-section .experience-card {
    height: 350px;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.experience-section .card-img-wrapper {
    height: 100%;
    background-size: cover;
    background-position: center;
    position: relative;
    transition: transform 0.5s ease;
}

.experience-section .card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.2);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.experience-section .experience-card:hover .card-img-wrapper {
    transform: scale(1.1); /* Zoom-Effekt */
}

.experience-section .experience-card:hover .card-overlay {
    opacity: 1; /* Farbüberlagerung bei Hover */
}

.experience-section .experience-card:hover {
    transform: translateY(-5px); /* Leichtes Anheben */
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.25); /* Stärkerer Schatten */
}

.experience-section .card-body {
    padding: 0;
    position: absolute;
    bottom: 0;
    width: 100%;
}

.experience-section .card-title-wrapper {
    width: fit-content;
    max-width: 100%;
    font-size: 1.2rem;
    transition: background-color 0.3s ease;
}

.experience-section .experience-card:hover .card-title-wrapper {
    background-color: rgba(255, 255, 255, 0.9); /* Leichte Farbänderung */
}

.experience-section .card-title-wrapper h4 {
    font-size: 1.5rem;
    font-weight: bold;
}

@media (max-width: 992px) {
    .experience-section .experience-card {
        height: 300px;
    }
}

@media (max-width: 768px) {
    .experience-section .experience-card {
        height: 250px;
    }
    .experience-section .card-title-wrapper h4 {
        font-size: 1.25rem;
    }
}
</style>
