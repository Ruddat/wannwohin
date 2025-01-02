<?php
// Im Controller oder direkt in der View zufällige Bilder abrufen
$randomImages = $location->gallery()->inRandomOrder()->take(2)->get();
?>
<section id="erleben" class="section section-no-border bg-color-primary m-0 py-5">
    <div class="container">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12 text-end">
                <h2 class="text-color-dark font-weight-extra-bold">Was kann man in {{ $location->title }} erleben?</h2>
            </div>
        </div>

        <!-- Content -->
        <div class="row align-items-center">
            <!-- Images -->
            <div class="col-md-4 text-center">
                @foreach ($randomImages as $key => $image)
                    <div class="d-inline-block position-relative mt-{{ $key > 0 ? 4 : 0 }}" style="transform: rotate({{ $key === 0 ? '-10deg' : '5deg' }});">
                        <button class="border-0 p-0" data-bs-toggle="modal" data-bs-target="#erleben_picture{{ $key + 1 }}_modal">
                            <img src="{{ Storage::url($image->image_path) }}"
                                 class="figure-img img-fluid rounded shadow-lg custom-border my-zoom"
                                 alt="{{ $image->description ?? 'Bild zu ' . $location->title }}">
                        </button>
                    </div>
                @endforeach
            </div>

            <!-- Text Content -->
            <div class="col-md-8 bg-white p-4 rounded shadow">
                @if (!empty($location->text_what_to_do))
                <div class="formatted-text">
                    {!! $location->text_what_to_do !!}
                </div>
            @else
                <p class="text-muted">Kein Text verfügbar.</p>
            @endif
            </div>
        </div>
    </div>
</section>

<!-- Modals for Images -->
@foreach ($randomImages as $key => $image)
<div class="modal fade" id="erleben_picture{{ $key + 1 }}_modal" tabindex="-1" aria-labelledby="erleben_picture{{ $key + 1 }}_label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-primary" id="erleben_picture{{ $key + 1 }}_label">{{ $location->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ Storage::url($image->image_path) }}" class="img-fluid rounded shadow" alt="{{ $image->description ?? 'Bild zu ' . $location->title }}">
                @if (!empty($image->description))
                    <p class="mt-3 text-muted">{{ $image->description }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach
