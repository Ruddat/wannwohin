<section class="section section-no-border bg-color-light m-0 pb-4 gallery-section" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); position: relative; overflow: hidden;">
    <div class="parallax-bg" style="background-image: url('https://www.transparenttextures.com/patterns/paper-fibers.png');"></div>
    <div class="container position-relative">
        <!-- Überschrift -->
        <div class="row mb-3">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-uppercase text-color-dark gallery-title animate__animated animate__fadeInDown">
                    Urlaubsfotos von {{ app('autotranslate')->trans($location->title, app()->getLocale()) }}
                </h2>
                <hr class="w-25 mx-auto gallery-divider" style="border: 3px solid #ffd700; opacity: 0.8;">
            </div>
        </div>

        <!-- Galerie -->
        @if ($gallery_images && count($gallery_images) > 0)
        <div class="row g-3 justify-content-center">
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
                    <div class="col-lg-4 col-md-6 col-sm-12" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                        <a href="{{ $imageUrl }}" class="glightbox" data-gallery="gallery"
                            @if ($displayText)
                            data-title="{{ app('autotranslate')->trans($displayText, app()->getLocale()) }}"
                            @endif>
                            <div class="polaroid-frame position-relative">
                                <div class="figure-img img-fluid custom-border"
                                     style="background-image: url('{{ $imageUrl }}');
                                            background-size: cover;
                                            background-position: center;
                                            height: {{ $displayText ? '200px' : '250px' }};">
                                    <div class="overlay"></div>
                                </div>
                                @if ($displayText)
                                <div class="polaroid-caption text-center p-2 bg-white small">
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
            <div class="text-center text-muted py-3">
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
        duration: 800,
        once: true,
    });
    const lightbox = GLightbox({
        selector: '.glightbox',
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
    /* Hintergrund und Parallax */
    .gallery-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        position: relative;
    }

    .parallax-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        opacity: 0.1;
        z-index: 0;
        transform: translateZ(0);
        will-change: transform;
    }

    /* Überschrift */
    .gallery-title {
        font-size: 2rem;
        color: #2d3748;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .gallery-divider {
        border-color: #ffd700;
        transition: width 0.3s ease;
    }

    .gallery-section:hover .gallery-divider {
        width: 50%;
    }

    /* Polaroid-Frame */
    .polaroid-frame {
        background: #fff;
        padding: 15px; /* Gleiches Padding, aber dynamische Höhe */
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
    }

    .custom-border {
        border-radius: 5px;
        overflow: hidden;
        transition: transform 0.3s ease-in-out;
    }

    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.2);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .polaroid-frame:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .polaroid-frame:hover .overlay {
        opacity: 1;
    }

    .col-lg-4:nth-child(odd) .polaroid-frame {
        transform: rotate(2deg);
    }

    .col-lg-4:nth-child(even) .polaroid-frame {
        transform: rotate(-2deg);
    }

    .col-lg-4:nth-child(odd) .polaroid-frame:hover {
        transform: scale(1.05) rotate(3deg);
    }

    .col-lg-4:nth-child(even) .polaroid-frame:hover {
        transform: scale(1.05) rotate(-3deg);
    }

    .polaroid-caption {
        font-size: 0.9rem;
        color: #333;
        background: #fff;
        position: relative; /* Nicht absolut, da es Teil des Frames ist */
        width: 100%;
        padding: 8px;
    }

    /* Responsive Anpassungen */
    @media (max-width: 768px) {
        .gallery-title {
            font-size: 1.5rem;
        }

        .figure-img {
            height: {{ $displayText ? '160px' : '200px' }};
        }

        .polaroid-frame {
            padding: 10px;
        }

        .polaroid-caption {
            font-size: 0.8rem;
            padding: 6px;
        }

        .col-lg-4:nth-child(odd) .polaroid-frame,
        .col-lg-4:nth-child(even) .polaroid-frame {
            transform: rotate(0);
        }
    }

    @media (max-width: 576px) {
        .gallery-title {
            font-size: 1.2rem;
        }

        .figure-img {
            height: {{ $displayText ? '140px' : '180px' }};
        }

        .polaroid-frame {
            padding: 8px;
        }

        .polaroid-caption {
            font-size: 0.75rem;
            padding: 5px;
        }
    }
</style>

<script>
    // Parallax-Effekt für den Hintergrund
    document.addEventListener('scroll', function () {
        const parallax = document.querySelector('.parallax-bg');
        if (parallax) {
            const scrollPosition = window.pageYOffset;
            parallax.style.transform = 'translateY(' + scrollPosition * 0.2 + 'px)';
        }
    });
</script>
