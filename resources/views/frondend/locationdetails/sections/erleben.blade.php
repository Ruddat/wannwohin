<?php
// Im Controller oder direkt in der View zufällige Bilder abrufen
$randomImages = $location->gallery()->inRandomOrder()->take(2)->get();
//dd($randomImages);

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
            <div class="col-md-4 text-center">
                @foreach ($randomImages as $key => $image)
                    @php
                        // Überprüfe, ob das Bild im Storage existiert
                        $imagePath = Storage::exists($image->image_path) ? Storage::url($image->image_path) : asset($image->image_path);
                    @endphp
                    <div class="d-inline-block position-relative mt-{{ $key > 0 ? 4 : 0 }}" style="transform: rotate({{ $key === 0 ? '-10deg' : '5deg' }});">
                        <button class="border-0 p-0" data-bs-toggle="modal" data-bs-target="#erleben_picture{{ $key + 1 }}_modal">
                            <img src="{{ $imagePath }}"
                                 class="figure-img img-fluid rounded shadow-lg custom-border my-zoom"
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
    opacity: 0.3; /* Hier die Transparenz einstellen */
    z-index: 1;
}

#erleben .container {
    position: relative;
    z-index: 2; /* Stellt sicher, dass der Inhalt über dem Bild bleibt */
   // background: rgba(255, 255, 255, 0.8); /* Optional: leichtes Weiß für bessere Lesbarkeit */
    padding: 20px;
    border-radius: 10px;
}
</style>
