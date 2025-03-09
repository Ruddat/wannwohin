@props(['position' => ''])

@php
    $ads = \App\Models\ModAdvertisementBlocks::where('is_active', 1)
        ->where('position', $position)
        ->inRandomOrder()
        ->limit(1)
        ->get();
@endphp

@forelse($ads as $ad)
    <div class="experience-card card border-0 shadow-lg h-100">
        <div class="card-body p-0">
            {!! $ad->script !!}
            <!-- Debugging: Anzeigen-ID -->
            <small class="text-muted">Ad ID: {{ $ad->id }}</small>
        </div>
    </div>
@empty
    <!-- Fallback: Platzhalter, falls keine Anzeige verfügbar -->
    <div class="experience-card card border-0 shadow-lg h-100">
        <div class="card-body text-muted d-flex align-items-center justify-content-center">
            Keine Werbung verfügbar
        </div>
    </div>
@endforelse
