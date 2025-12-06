<!-- resources/views/partials/picture.blade.php -->
<section class="section section-no-border m-0 pb-5 gallery-section" style="position: relative; overflow: hidden;">
    <!-- Parallax-Hintergrund -->
    <div class="parallax-bg"
         style="background-image: url('/assets/img/erleben_pictures.jpg');"
         data-jarallax
         data-speed="0.5">
    </div>

    <div class="container position-relative z-index-2">
        <!-- Überschrift -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-uppercase text-color-dark animate__animated animate__fadeInDown">
                    Urlaubsfotos von {{ app('autotranslate')->trans($location->title, app()->getLocale()) }}
                </h2>
                <hr class="w-25 mx-auto gallery-divider" style="border: 2px solid #007bff;">
            </div>
        </div>

        <!-- Galerie -->
        @if ($gallery_images && count($gallery_images) > 0)
        <div class="row g-3">
            @foreach ($gallery_images as $index => $image)
@php
    // RAW path from DB or API
    $rawPath = $image['url'] ?? $image['image_path'] ?? null;

    // convert to correct URL
    if ($rawPath) {
        // if absolute URL -> keep it
        if (str_starts_with($rawPath, 'http')) {
            $imageUrl = $rawPath;
        } else {
            // convert storage path
            $imageUrl = asset('storage/' . ltrim($rawPath, '/'));
        }
    } else {
        $imageUrl = null;
    }

    // captions
    $imageCaption = $image['image_caption'] ?? null;
    $imageDescription = $image['description'] ?? null;

    $displayText = $imageCaption ?: $imageDescription ?: null;
@endphp

@if ($imageUrl)
    <div class="col-lg-4 col-md-6 col-sm-12" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
        <a href="{{ $imageUrl }}" class="glightbox" data-gallery="gallery"
            @if ($displayText)
                data-title="{{ app('autotranslate')->trans($displayText, app()->getLocale()) }}"
            @endif
        >
            <div class="polaroid-frame position-relative">
                <div class="figure-img img-fluid custom-border"
                    style="background-image: url('{{ $imageUrl }}'); background-size: cover; background-position: center; height: 200px;">
                </div>

                @if ($displayText)
                    <div class="polaroid-caption text-center p-2 bg-white small">
                        <span>{{ app('autotranslate')->trans($displayText, app()->getLocale()) }}</span>
                    </div>
                @endif
            </div>
        </a>
    </div>
@else
    <div class="col-lg-4 col-md-6 col-sm-12 text-center text-danger">
        Ungültige Bild-URL
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

{{--
<!-- GLightbox (bleibt vorerst CDN) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>



<!-- AOS (in npm) -->
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

--}}

<style>
    /* Parallax-Hintergrund */
    .parallax-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        opacity: 0.5;
        z-index: 1;
        transform: translateZ(0);
        will-change: transform;
    }

    .z-index-2 {
        z-index: 2;
    }

    /* Gallery Section */
    .gallery-section {
        background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
        position: relative;
        z-index: 1;
    }

    .gallery-divider {
        border-color: #007bff;
        transition: width 0.3s ease;
    }

    .gallery-section:hover .gallery-divider {
        width: 50%;
    }

    /* Polaroid-Frame */
    .polaroid-frame {
        background: #fff;
        padding: 15px 15px 30px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        cursor: pointer;
        z-index: 2;
    }

    .custom-border {
        border-radius: 5px;
        overflow: hidden;
        transition: transform 0.3s ease-in-out;
    }

    .figure-img {
        height: 200px;
    }

    .polaroid-caption {
        font-size: 0.9rem;
        color: #333;
        background: #fff;
        position: relative;
        width: 100%;
        padding: 8px;
    }

    .col-lg-4:nth-child(odd) .polaroid-frame {
        transform: rotate(2deg);
    }

    .col-lg-4:nth-child(even) .polaroid-frame {
        transform: rotate(-2deg);
    }

    .col-lg-4:nth-child(odd) .polaroid-frame:hover {
        transform: scale(1.05) rotate(3deg);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .col-lg-4:nth-child(even) .polaroid-frame:hover {
        transform: scale(1.05) rotate(-3deg);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    @media (max-width: 768px) {
        .figure-img { height: 160px; }
        .polaroid-frame { padding: 10px 10px 20px 10px; }
        .polaroid-caption { font-size: 0.8rem; padding: 6px; }
        .col-lg-4:nth-child(odd) .polaroid-frame, .col-lg-4:nth-child(even) .polaroid-frame { transform: rotate(0); }
        .col-lg-4:nth-child(odd) .polaroid-frame:hover, .col-lg-4:nth-child(even) .polaroid-frame:hover { transform: scale(1.05) rotate(0deg); }
    }

    @media (max-width: 576px) {
        .figure-img { height: 140px; }
        .polaroid-frame { padding: 8px 8px 15px 8px; }
        .polaroid-caption { font-size: 0.75rem; padding: 5px; }
    }
</style>

@push('scripts')
@vite(['resources/backend/js/app.js']) <!-- Jarallax wird hierüber geladen -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // AOS Initialisierung
    AOS.init({
        duration: 1000,
        once: true,
    });
    console.log('AOS initialisiert');

    // GLightbox Initialisierung
    try {
        const lightbox = GLightbox({
            selector: '.glightbox',
            loop: true,
            touchNavigation: true,
            zoomable: true,
            openEffect: 'zoom',
            closeEffect: 'fade',
            slideThumbnails: true,
            touchFollowAxis: true
        });
        console.log('GLightbox erfolgreich initialisiert');
    } catch (error) {
        console.error('Fehler bei GLightbox-Initialisierung:', error);
    }
});
</script>
@endpush
