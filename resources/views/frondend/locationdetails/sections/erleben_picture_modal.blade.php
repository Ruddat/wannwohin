<section class="section section-no-border bg-color-light m-0 pb-5" style="background-color: #f8f9fa;">
    <div class="container">
        <!-- Überschrift -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-uppercase">Urlaubsfotos von {{ $location->title }}</h2>
                <hr class="w-25 mx-auto" style="border: 2px solid #007bff;">
            </div>
        </div>

        <!-- Galerie -->
        <div class="row g-3">
            @foreach ($gallery_images as $index => $image)
                @php
                    $imageUrl = trim($image['url']); // URL bereinigen
                @endphp
                @if (filter_var($imageUrl, FILTER_VALIDATE_URL)) <!-- Sicherstellen, dass die URL gültig ist -->
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <a href="{{ $imageUrl }}" class="glightbox" data-gallery="gallery" data-title="{{ $image['description'] }}">
                            <div
                                class="figure-img img-fluid custom-border position-relative"
                                style="background-image: url('{{ $imageUrl }}');
                                       background-size: cover;
                                       background-position: center;
                                       height: 250px; border-radius: 8px;">
                                <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-opacity-50 bg-dark text-white text-center small">
                                    <span>{{ $image['description'] ?? 'Bild ' . ($index + 1) }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>


        <!-- Miniaturansicht -->
        <div class="mt-4 text-center d-none d-md-flex justify-content-center">
            @foreach ($gallery_images as $index => $image)
            @if (filter_var($image['url'], FILTER_VALIDATE_URL))
            <div class="mx-2">
                <a href="{{ $image['url'] }}" class="glightbox-thumbnail" data-gallery="gallery" data-title="{{ $image['description'] }}">
                    <img src="{{ $image['url'] }}" alt="{{ $image['description'] }}" class="img-thumbnail" style="width: 75px; height: 50px; object-fit: cover;">
                </a>
            </div>
            @endif
            @endforeach
        </div>

    </div>
</section>

<!-- GLightbox -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
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

    // Verhindert `aria-hidden` auf aktiven Lightbox-Elementen
    lightbox.on('open', () => {
        const lightboxWrapper = document.querySelector('.glightbox-container');
        if (lightboxWrapper) {
            lightboxWrapper.removeAttribute('aria-hidden');
        }
    });
});
</script>
