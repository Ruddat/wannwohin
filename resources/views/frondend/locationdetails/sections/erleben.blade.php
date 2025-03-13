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
                    // Zufällige Rotation zwischen -8 und 8 Grad
                    $rotationValue = rand(-8, 8);
                    // Dynamische vertikale Positionierung
                    $topOffset = ($key * (90 / $imageCount)) . '%'; // 90% für kompaktere Verteilung
                    // Horizontale Positionierung
                    $horizontalOffset = rand(40, 80);
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
            <div class="col-md-8 bg-white p-3 rounded shadow">
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
        opacity: 0.25; /* Etwas dezenter */
        z-index: 1;
    }

    #erleben .container {
        position: relative;
        z-index: 2;
        padding: 15px; /* Kompakter */
        border-radius: 8px;
    }

    .gallery-images {
        position: relative;
        height: 100%;
        min-height: 360px; /* Reduzierte Mindesthöhe */
        display: flex;
        flex-direction: column;
        justify-content: space-around;
        align-items: flex-end;
        padding: 20px 0; /* Weniger Padding */
    }

    .gallery-image {
        position: absolute;
        transition: transform 0.3s ease-in-out;
        z-index: 5;
        margin: 20px 0; /* Reduzierter Abstand */
    }

    .gallery-image img {
        width: 280px; /* Etwas kleiner */
        max-width: 100%;
        height: auto;
        object-fit: cover;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15); /* Subtiler Schatten */
    }

    .gallery-image:hover {
        transform: scale(1.1) rotate(0deg); /* Etwas kleinerer Zoom */
        z-index: 10;
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
            padding-bottom: 2rem !important; /* Kompakter */
        }

        .mb-3 {
            margin-bottom: 1rem !important; /* Weniger Abstand */
        }

        .p-3 {
            padding: 1.5rem !important; /* Weniger Padding */
        }
    }
</style>
