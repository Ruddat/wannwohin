<?php
// Text vorbereiten
$rawText = strip_tags($location->text_what_to_do); // HTML-Tags entfernen
$textLength = strlen($rawText); // Zeichenlänge des Textes

// Bildanzahl basierend auf der Zeichenlänge (1 bis 5 Bilder)
$imageCount = max(1, min(ceil($textLength / 500), 5));
$randomImages = $location->gallery()->inRandomOrder()->take($imageCount)->get();
?>

<section id="erleben" class="section section-no-border bg-color-primary m-0 py-4 position-relative">
    <div class="background-overlay"></div>
    <div class="container position-relative">
        <!-- Header -->
        <div class="row mb-3">
            <div class="col-12 text-end">
                <h2 class="text-color-dark font-weight-extra-bold">
                    @autotranslate("Was ist die beste Reisezeit {$location->title} zu besuchen?", app()->getLocale())
                </h2>
            </div>
        </div>

        <!-- Content -->
        <div class="row align-items-center">
            <!-- Images -->
            <div class="col-md-4 text-center gallery-images">
                @foreach ($randomImages as $key => $image)
                    @php
                        // Zufällige Rotation zwischen -5 und 5 Grad für Polaroid-Effekt
                        $rotationValue = rand(-5, 5);
                        $imagePath = Storage::exists($image->image_path) ? Storage::url($image->image_path) : asset($image->image_path);
                    @endphp
                    <div class="gallery-image" style="transform: rotate({{ $rotationValue }}deg);">
                        <!-- Bild in einen Container einbetten, der den Schimmer-Effekt erhält -->
                        <div class="polaroid">
                            <button class="border-0 p-0" data-bs-toggle="modal" data-bs-target="#erleben_picture{{ $key + 1 }}_modal">
                                <img src="{{ $imagePath }}" class="figure-img img-fluid" alt="@autotranslate($image->description ?? 'Bild zu ' . $location->title, app()->getLocale())">
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Text Content -->
            <div class="col-md-8 bg-white p-3 rounded shadow text-content">
                @if (!empty($location->text_what_to_do))
                    <div class="formatted-text">
                        {!! app('autotranslate')->trans($location->text_what_to_do, app()->getLocale()) !!}
                    </div>
                @else
                    <p class="text-muted">@autotranslate('Kein Text verfügbar.', app()->getLocale())</p>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Modals for Images -->
@foreach ($randomImages as $key => $image)
    @php
        $modalImagePath = Storage::exists($image->image_path) ? Storage::url($image->image_path) : asset($image->image_path);
    @endphp
    <div class="modal fade" id="erleben_picture{{ $key + 1 }}_modal" tabindex="-1" aria-labelledby="erleben_picture{{ $key + 1 }}_label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-primary" id="erleben_picture{{ $key + 1 }}_label">
                        @autotranslate($location->title, app()->getLocale())
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ $modalImagePath }}" class="img-fluid rounded shadow"
                         alt="@autotranslate($image->description ?? 'Bild zu ' . $location->title, app()->getLocale())">
                    @if (!empty($image->description))
                        <p class="mt-3 text-muted">@autotranslate($image->description, app()->getLocale())</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach

<style>
    .background-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('/assets/img/slider.jpg') no-repeat center center;
        background-size: cover;
        opacity: 0.25;
        z-index: 1;
    }

    #erleben .container {
        position: relative;
        z-index: 2;
        padding: 15px;
        border-radius: 8px;
    }

    .gallery-images {
        display: flex;
        flex-direction: column;
        gap: 10px; /* Abstand zwischen den Bildern */
        align-items: center;
        justify-content: center;
        min-height: 360px;
        padding: 20px 0;
    }

    /* Container für das Bild mit Polaroid- und Schimmer-Effekt */
    .polaroid {
        width: 250px;
        max-width: 100%;
        position: relative; /* wichtig für das ::after Pseudo-Element */
        overflow: hidden;   /* verhindert, dass der Schimmer aus dem Container herausragt */
        background: #fff;
        padding: 10px 10px 30px 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border: 1px solid #ddd;
        transition: transform 0.3s ease-in-out;
    }

    /* Schimmer-Effekt */
    .polaroid::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            120deg,
            rgba(255, 255, 255, 0) 0%,
            rgba(255, 255, 255, 0.8) 50%,
            rgba(255, 255, 255, 0) 100%
        );
        opacity: 0;
        transform: translateX(-100%);
        transition: opacity 0.3s ease;
    }

    /* Hover-Effekt: Container skalieren und Schimmer anzeigen */
    .gallery-image:hover .polaroid {
        transform: scale(1.05) rotate(2deg);
    }

    .gallery-image:hover .polaroid::after {
        opacity: 1;
        animation: shimmer 1.5s infinite;
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
            opacity: 0;
        }
        50% {
            opacity: 1;
        }
        100% {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    /* Textbereich */
    .formatted-text {
        font-size: 1rem;
        line-height: 1.5;
        color: #333;
    }

    /* Responsive Anpassungen */
    @media (max-width: 768px) {
        .gallery-images {
            display: none; /* Bilder auf Mobilgeräten ausblenden */
        }

        .col-md-8 {
            width: 100%; /* Text nimmt volle Breite ein */
        }

        #erleben .container {
            padding: 10px;
        }

        .py-4 {
            padding-top: 2rem !important;
            padding-bottom: 2rem !important;
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .p-3 {
            padding: 1.5rem !important;
        }
    }
</style>
