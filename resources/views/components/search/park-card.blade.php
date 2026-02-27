@props(['park'])

@php
    $rating   = $park->avg_rating ?? null;
    $coolness = $park->coolness_score ?? null;
    $distance = $park->distance ?? null;
    $country  = $park->country_name ?? null;
    $slug     = $park->slug ?? null;

    // Dynamisches Badge aus erstem Park-Tag
    $tagLabel = 'Freizeitpark';
    if (!empty($park->tags) && count($park->tags)) {
        $firstTag = $park->tags[0] ?? null;
        $tagLabel = $firstTag->title ?? $tagLabel;
    }
@endphp

<div class="card park-card mb-4 border-0 shadow-sm">

    <div class="row g-0 align-items-stretch">

        {{-- Bild --}}
        <div class="col-md-4 park-image-wrapper">
            <a href="{{ route('parks.show', $slug) }}">
                <img
                    src="{{ $park->logo_url ?? asset('images/park-placeholder.jpg') }}"
                    class="park-image"
                    alt="{{ $park->name }}"
                >
            </a>
        </div>

        {{-- Content --}}
        <div class="col-md-8">
            <div class="card-body d-flex flex-column h-100 p-4">

                <div class="mb-2">

                    <span class="badge park-badge mb-2">
                        🎢 {{ $tagLabel }}
                    </span>

                    <h6 class="fw-semibold mb-1">
                        <a href="{{ route('parks.show', $slug) }}"
                           class="text-decoration-none text-dark park-title">
                            {{ $park->name }}
                        </a>
                    </h6>

                    @if($country)
                        <div class="text-muted small">
                            {{ $country }}
                            @if($distance)
                                · {{ round($distance,1) }} km entfernt
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Bewertung + Coolness kompakt --}}
                @if($rating || $coolness)
                    <div class="small text-muted mb-2">
                        @if($rating)
                            ★ {{ number_format($rating,1) }}
                        @endif
                        @if($coolness)
                            · 🔥 {{ $coolness }}
                        @endif
                    </div>
                @endif

                @if(!empty($park->description))
                    <p class="text-muted small mb-4">
                        {{ \Illuminate\Support\Str::limit(strip_tags($park->description), 140) }}
                    </p>
                @endif

                <div class="mt-auto">

                    @if(!empty($park->affiliate_enabled))
                        <a href="{{ route('park.go', [$slug, 'primary']) }}"
                           class="btn btn-success btn-sm fw-semibold"
                           rel="nofollow sponsored">
                            🎟 Tickets sichern
                        </a>
                    @else
                        <a href="{{ route('parks.show', $slug) }}"
                           class="btn btn-outline-dark btn-sm">
                            Details ansehen
                        </a>
                    @endif

                </div>

            </div>
        </div>

    </div>
</div>

<style>
.park-card {
    border-radius: 16px;
    transition: all 0.25s ease;
    overflow: hidden;
    background: #fcfcfc;
}

.park-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.06);
}

.park-image-wrapper {
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.park-image {
    max-height: 150px;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.park-card:hover .park-image {
    transform: scale(1.04);
}

.park-badge {
    background: #198754;
    font-size: 0.72rem;
    padding: 5px 10px;
    border-radius: 20px;
}

.park-title:hover {
    color: #198754;
}
</style>
