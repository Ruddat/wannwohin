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
<!-- Galerie -->
<div class="row g-3">
    @foreach ($gallery_images as $index => $image)
        @php
            $imageUrl = trim($image['url'] ?? '');
        @endphp
        @if (filter_var($imageUrl, FILTER_VALIDATE_URL))
            <div class="col-lg-4 col-md-6 col-sm-12">
                <a href="{{ $imageUrl }}" class="glightbox" data-gallery="gallery"
                    data-title="{{ app('autotranslate')->trans($image['image_caption'] ?? $image['description'] ?? 'Bild ' . ($loop->iteration), app()->getLocale()) }}">
                    <div
                        class="figure-img img-fluid custom-border position-relative lazyload"
                        data-bg="{{ $imageUrl }}"
                        style="background-size: cover;
                               background-position: center;
                               height: 250px; border-radius: 8px;">
                        <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-opacity-50 bg-dark text-white text-center small">
                            <span>{{ app('autotranslate')->trans($image['image_caption'] ?? $image['description'] ?? 'Bild ' . ($loop->iteration), app()->getLocale()) }}</span>
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
                    @php
                        $thumbnailUrl = trim($image['url'] ?? '');
                    @endphp
                    @if (filter_var($thumbnailUrl, FILTER_VALIDATE_URL))
                        <div class="mx-2">
                            <a href="{{ $thumbnailUrl }}" class="glightbox-thumbnail" data-gallery="gallery"
                                data-title="{{ app('autotranslate')->trans($image['image_caption'] ?? $image['description'] ?? 'Bild ' . ($loop->iteration), app()->getLocale()) }}">
                                <img src="{{ $thumbnailUrl }}" alt="{{ app('autotranslate')->trans($image['image_caption'] ?? $image['description'] ?? 'Bild ' . ($loop->iteration), app()->getLocale()) }}"
                                    class="img-thumbnail" style="width: 75px; height: 50px; object-fit: cover;">
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
    // Lazy Load Hintergrundbilder
    const lazyBackgrounds = document.querySelectorAll('.lazyload');

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    const bgUrl = element.getAttribute('data-bg');
                    if (bgUrl) {
                        element.style.backgroundImage = `url('${bgUrl}')`;
                        element.classList.add('loaded');
                        observer.unobserve(element);
                    }
                }
            });
        });

        lazyBackgrounds.forEach(bg => observer.observe(bg));
    } else {
        // Fallback für alte Browser (alle Bilder sofort laden)
        lazyBackgrounds.forEach(bg => {
            const bgUrl = bg.getAttribute('data-bg');
            if (bgUrl) {
                bg.style.backgroundImage = `url('${bgUrl}')`;
                bg.classList.add('loaded');
            }
        });
    }

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

    .custom-border:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }
    .lazyload {
    background-color: #f0f0f0; /* Platzhalterfarbe */
    background-size: cover;
    background-position: center;
    height: 250px;
    transition: opacity 0.3s ease-in-out;
    opacity: 0;
}

.lazyload.loaded {
    opacity: 1;
}
</style>
