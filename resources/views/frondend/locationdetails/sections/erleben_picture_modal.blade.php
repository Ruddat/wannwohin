

<section class="section section-no-border bg-color-light m-0 pb-5" style="background-color: #f8f9fa;">
    <div class="container">
        <!-- Überschrift -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-uppercase">
                    Urlaubsfotos von {{ app('autotranslate')->trans($location->title, app()->getLocale()) }}
                </h2>
                <hr class="w-25 mx-auto" style="border: 2px solid #007bff;">
            </div>
        </div>

        <!-- Galerie -->
        @if ($gallery_images && count($gallery_images) > 0)
        <div class="row g-3">
            @foreach ($gallery_images as $index => $image)
                @php
                    $imageUrl = trim($image['url'] ?? '');
                    $imageCaption = $image['image_caption'] ?? null;
                    $imageDescription = $image['description'] ?? null;

                    $captionText = (!empty($imageCaption) && $imageCaption !== 'Kein Titel verfügbar') ? $imageCaption : null;
                    $descriptionText = (!empty($imageDescription) && $imageDescription !== 'Keine Beschreibung verfügbar') ? $imageDescription : null;

                    $displayText = $captionText ?? $descriptionText;
                @endphp
                @if (filter_var($imageUrl, FILTER_VALIDATE_URL))
                    <div class="col-lg-4 col-md-6 col-sm-12" data-aos="fade-up">
                        <a href="{{ $imageUrl }}" class="glightbox" data-gallery="gallery"
                            @if ($displayText)
                            data-title="{{ app('autotranslate')->trans($displayText, app()->getLocale()) }}"
                            @endif>
                            <div
                            class="figure-img img-fluid custom-border position-relative"
                            style="background-image: url('{{ $imageUrl }}');
                                   background-size: cover;
                                   background-position: center;
                                   height: 250px;">
                                @if ($displayText)
                                <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-opacity-50 bg-dark text-white text-center small">
                                    <span>{{ app('autotranslate')->trans($displayText, app()->getLocale()) }}</span>
                                </div>
                                @endif
                            </div>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
        @else
            <div class="text-center text-muted py-4">
                <p>Es sind derzeit keine Bilder verfügbar.</p>
            </div>
        @endif
    </div>
</section>


<!-- GLightbox -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    AOS.init({
        duration: 1000, // Dauer der Animation in ms
        once: true,     // Animation nur einmal abspielen
    });
    // GLightbox Initialisierung
    const lightbox = GLightbox({
        selector: '.glightbox, .glightbox-thumbnail',
        loop: true,
        touchNavigation: true,
        zoomable: true,
        openEffect: 'zoom',
        closeEffect: 'fade',
        slideThumbnails: true,
        touchFollowAxis: true,
    });
});
</script>

<style>
    .custom-border {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }

    /* Abwechselnde Drehung basierend auf den Containern */
    .col-lg-4:nth-child(odd) .custom-border:hover {
        transform: scale(1.05) rotate(5deg); /* Dreht nach rechts */
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }

    .col-lg-4:nth-child(even) .custom-border:hover {
        transform: scale(1.05) rotate(-5deg); /* Dreht nach links */
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }
</style>

