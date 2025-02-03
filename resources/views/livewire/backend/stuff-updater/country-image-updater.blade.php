<div class="container mt-4">
    <h3>Country Image Updater</h3>
    <p>Automatisch fehlende Bilder für Länder mit der Pixabay API abrufen.</p>

    <div class="mb-3">
        <button wire:click="updateImages" class="btn btn-primary" @if ($currentIndex >= $totalCountries) disabled @endif>
            <i class="ti ti-refresh"></i> Update Image
        </button>
    </div>

    <!-- Fortschrittsanzeige -->
    <div class="progress mb-3">
        <div
            class="progress-bar"
            role="progressbar"
            style="width: {{ $progress }}%;"
            aria-valuenow="{{ $progress }}"
            aria-valuemin="0"
            aria-valuemax="100"
        >
            {{ $progress }}%
        </div>
    </div>

    <p>{{ $statusMessage }}</p>

    <!-- Zeige aktuelles Land mit Vorschaubild -->
    @if ($currentCountry)
    <div class="card mt-3">
        <div class="card-body text-center">
            <h5 class="card-title">{{ $currentCountry->title }}</h5>

            <!-- Überprüfen, ob ein Bild vorhanden ist, ansonsten Dummy-Bild anzeigen -->
            @php
                $imagePath = $currentCountry->image1_path
                    ? asset('storage/' . $currentCountry->image1_path)
                    : asset('img/default-country-thumbnail.png'); // Dummy-Bild
            @endphp

            <img src="{{ $imagePath }}" alt="Thumbnail" class="img-thumbnail" style="max-width: 200px;">
            <p class="text-muted">Aktualisiertes Bild für {{ $currentCountry->title }}</p>
        </div>
    </div>
@endif

    @if ($currentIndex >= $totalCountries)
        <div class="alert alert-success mt-3">
            Alle Bilder wurden aktualisiert!
        </div>
    @endif
</div>
