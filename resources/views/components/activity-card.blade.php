@props([
    'title',
    'description',
    'category' => 'Erlebnis',
    'icon' => 'fa-map-location-dot',
    'duration' => '1–2 Stunden',
    'location' => 'Zentrumsnah',
    'rating' => '90 %',
])

<div class="card border-0 shadow-sm mb-4 rounded-4">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid {{ $icon }} fs-4 text-primary"></i>
                <h5 class="card-title mb-0">{{ $title }}</h5>
            </div>
            <span class="badge bg-warning text-dark">{{ $category }}</span>
        </div>

        <p class="card-text mt-2 text-muted">{!! $description !!}</p>

        <ul class="list-inline text-muted small mb-3">
            <li class="list-inline-item"><i class="fa-regular fa-clock"></i> Dauer: {{ $duration }}</li>
            <li class="list-inline-item"><i class="fa-solid fa-sun"></i> Beste Zeit: {{ $season ?? 'Ganzjährig' }}</li>
            <li class="list-inline-item"><i class="fa-solid fa-thumbs-up"></i> {{ $rating }}</li>
        </ul>

        <div class="d-flex gap-2">
            {{ $buttons ?? '' }}
            <button class="btn btn-outline-secondary btn-sm" title="Favorisieren">
                <i class="fa-regular fa-heart"></i>
            </button>
        </div>
    </div>
</div>
