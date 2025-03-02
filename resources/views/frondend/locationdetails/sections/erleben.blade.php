<?php
// Text vorbereiten
$rawText = strip_tags($location->text_what_to_do); // HTML-Tags entfernen
$textLength = strlen($rawText); // Zeichenlänge des Textes

// Bildanzahl basierend auf der Zeichenlänge berechnen
$imageCount = max(1, min(ceil($textLength / 500), 5));
$randomImages = $location->gallery()->inRandomOrder()->take($imageCount)->get();
?>

<section id="erleben" class="section section-no-border bg-color-primary m-0 py-5 position-relative">
    <div class="background-overlay"></div>
    <div class="container position-relative">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12 text-end">
                <h2 class="text-color-dark font-weight-extra-bold">
                    @autotranslate("Was kann man in {$location->title} erleben?", app()->getLocale())
                </h2>
            </div>
        </div>

        <!-- Content -->
        <div class="row align-items-center">
            <!-- Images -->
            <div class="col-md-4 text-center gallery-images">
                @foreach ($randomImages as $key => $image)
                @php
                    // Zufällige Rotation zwischen -10 und 10 Grad
                    $rotationValue = rand(-10, 10);
                    // Dynamische vertikale Positionierung mit mehr Abstand
                    $topOffset = ($key * (100 / $imageCount)) . '%'; // 120% statt 100%, um mehr Raum zu schaffen
                    // Horizontale Positionierung nach rechts verschoben
                    $horizontalOffset = rand(50, 100);
                    $positionStyle = "top: {$topOffset}; left: {$horizontalOffset}px; transform: rotate({$rotationValue}deg);";

                    $imagePath = Storage::exists($image->image_path) ? Storage::url($image->image_path) : asset($image->image_path);
                @endphp
                <div class="gallery-image position-absolute" style="{{ $positionStyle }}">
                    <button class="border-0 p-0" data-bs-toggle="modal" data-bs-target="#erleben_picture{{ $key + 1 }}_modal">
                        <img src="{{ $imagePath }}" class="figure-img img-fluid rounded shadow-lg custom-border my-zoom"
                             alt="@autotranslate($image->description ?? 'Bild zu ' . $location->title, app()->getLocale())">
                    </button>
                </div>
                @endforeach
            </div>

            <!-- Text Content -->
            <div class="col-md-8 bg-white p-4 rounded shadow">
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
        // Überprüfe, ob das Bild im Storage existiert
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
        opacity: 0.3;
        z-index: 1;
    }

    #erleben .container {
        position: relative;
        z-index: 2;
        padding: 20px;
        border-radius: 10px;
    }

    .gallery-images {
        position: relative;
        height: 100%; /* Passt sich der Höhe des Textes an */
        min-height: 420px; /* Erhöhte Mindesthöhe für mehr Platz */
        display: flex;
        flex-direction: column;
        justify-content: space-around; /* Vertikale Verteilung */
        align-items: flex-end; /* Bilder nach rechts ausrichten */
        padding: 30px 0; /* Mehr vertikaler Innenabstand */
        margin-bottom: auto; /* Text nach oben schieben */
    }

    .gallery-image {
        position: absolute;
        transition: transform 0.3s ease-in-out;
        z-index: 5;
        margin: 40px 0; /* Mehr vertikaler Abstand zwischen Bildern */
    }

    .gallery-image img {
        width: 300px; /* Größere Bilder */
        max-width: 100%;
        height: auto;
        object-fit: cover;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Leichter Schatten */
    }

    .gallery-image:hover {
        transform: scale(1.15) rotate(0deg); /* Stärkerer Zoom */
        z-index: 10;
    }

    @media (max-width: 768px) {
        .gallery-images {
            min-height: auto;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center; /* Zentriert auf Mobilgeräten */
            padding: 20px;
        }

        .gallery-image {
            position: relative;
            top: 0 !important;
            left: 0 !important;
            transform: rotate(0deg) !important; /* Keine Rotation auf Mobilgeräten */
            margin: 25px; /* Mehr Abstand auf Mobilgeräten */
        }

        .gallery-image img {
            width: 250px; /* Etwas kleiner auf Mobilgeräten */
        }
    }
    </style>

