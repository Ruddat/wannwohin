@props(['position' => ''])

@php
    $ads = \App\Models\ModAdvertisementBlocks::where('is_active', 1)
        ->where(function ($query) use ($position) {
            $query->whereJsonContains('position', $position) // Prüft, ob $position im Array enthalten ist
                  ->orWhere('position', $position); // Für alte Einträge mit String
        })
        ->inRandomOrder()
        ->limit(1)
        ->get();

    // Debugging: Prüfen, ob Anzeigen gefunden werden (aktiviere für Tests)
    //dump("Position: $position, Ads found: " . $ads->count());
    //if ($ads->count() > 0) {
    //    dump("Ad ID: " . $ads->first()->id . ", Script: " . $ads->first()->script);
    //}
@endphp

@forelse($ads as $ad)
    <div class="experience-card card border-0 shadow-lg ad-card full-width">
        <div class="card-body p-0 d-flex flex-column justify-content-between h-100">
            <div class="ad-content d-flex align-items-center justify-content-center flex-grow-1 position-relative">
                <div class="banner-wrapper w-100 h-100">
                    <div class="banner-inner">
                        {!! $ad->script !!}
                    </div>
                </div>
            </div>
            <div class="ad-footer">
                <small class="text-muted">Werbung | Ad ID: {{ $ad->id }}</small>
            </div>
        </div>
    </div>
@empty
    <div class="experience-card card border-0 shadow-lg ad-card full-width">
        <div class="card-body text-muted d-flex align-items-center justify-content-center">
            Keine Werbung verfügbar für Position: {{ $position }}
        </div>
    </div>
@endforelse
