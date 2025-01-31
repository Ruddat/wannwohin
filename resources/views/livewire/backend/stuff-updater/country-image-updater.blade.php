<div class="container mt-4">
    <h3>Country Image Updater</h3>
    <p>Automatically fetch missing images for countries using Pixabay API.</p>

    <div class="mb-3">
        <button wire:click="updateImages" class="btn btn-primary" @if ($currentIndex >= $totalCountries) disabled @endif>
            Update Images
        </button>
    </div>

    <div class="progress mb-3">
        <div
            class="progress-bar"
            role="progressbar"
            style="width: {{ $progress }}%;"
            aria-valuenow="{{ $progress }}"
            aria-valuemin="0"
            aria-valuemax="100"
            wire:poll="updateImages1"
        >
            {{ $progress }}%
        </div>
    </div>

    <p>{{ $statusMessage }}</p>

    @if ($currentIndex >= $totalCountries)
        <div class="alert alert-success">
            All images have been updated.
        </div>
    @endif
</div>


