@extends('layouts.main')

@section('content')
@php
    // defensive defaults
    $logo = $park->logo_url ?: asset('images/park-placeholder.jpg');
    $country = $park->country ?? null;
    $location = $park->location ?? null;

    $rating = $park->avg_rating ?? null;
    $coolness = $park->coolness_score ?? null;
    $comments = $park->comment_count ?? null;

    // falls du opening_hours als JSON speicherst
    $opening = null;
    if (!empty($park->opening_hours)) {
        $opening = is_array($park->opening_hours)
            ? $park->opening_hours
            : json_decode($park->opening_hours, true);
    }

    $hasVideo = !empty($park->embed_code) || !empty($park->video_url);
@endphp

<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="/">Start</a></li>
            <li class="breadcrumb-item"><a href="/suche">Suche</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $park->name }}</li>
        </ol>
    </nav>

    {{-- Header Card --}}
    <div class="card border-0 shadow-sm overflow-hidden mb-4 park-show-card">
        <div class="row g-0">
            <div class="col-lg-4 park-show-logo">
                <img src="{{ $logo }}" alt="{{ $park->name }}" class="w-100 h-100">
            </div>

            <div class="col-lg-8">
                <div class="card-body p-4">

                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-2">
                        <div>
                            <span class="badge bg-success mb-2">🎢 Freizeitpark</span>
                            <h1 class="h3 fw-bold mb-1">{{ $park->name }}</h1>

                            <div class="text-muted">
                                @if($country) {{ $country }} @endif
                                @if($location) <span class="mx-1">·</span> {{ $location }} @endif
                            </div>
                        </div>

                        {{-- Scorebox --}}
                        <div class="text-end">
                            @if($rating)
                                <div class="fw-bold text-warning">★ {{ number_format($rating,1) }}</div>
                                <div class="small text-muted">Bewertung</div>
                            @endif

                            @if($coolness)
                                <div class="mt-2">
                                    <span class="badge bg-dark">🔥 Coolness {{ (int)$coolness }}/100</span>
                                </div>
                            @endif

                            @if($comments !== null)
                                <div class="small text-muted mt-1">{{ (int)$comments }} Kommentare</div>
                            @endif
                        </div>
                    </div>

                    @if(!empty($park->description))
                        <p class="text-muted mb-3">
                            {!! nl2br(e(strip_tags($park->description))) !!}
                        </p>
                    @endif

                    {{-- CTA Row --}}
                    <div class="d-flex flex-wrap gap-2">
                        @if(!empty($park->website))
                            <a href="{{ $park->website }}" target="_blank" rel="nofollow noopener"
                               class="btn btn-outline-dark">
                                🌐 Offizielle Website
                            </a>
                        @endif

                        @if(!empty($park->affiliates))
                            <a href="{{ route('park.go', [$park->external_id, 'primary']) }}"
                               class="btn btn-success fw-semibold"
                               rel="nofollow sponsored">
                                🎟 Tickets sichern
                            </a>
                        @endif

                        @if($park->latitude && $park->longitude)
                            <a class="btn btn-outline-secondary"
                               target="_blank" rel="nofollow noopener"
                               href="https://www.google.com/maps?q={{ $park->latitude }},{{ $park->longitude }}">
                                📍 Karte öffnen
                            </a>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="row g-4">

        {{-- Öffnungszeiten --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">🕒 Öffnungszeiten</h2>

                    @if(is_array($opening))
                        @php
                            $days = [
                                'monday' => 'Montag',
                                'tuesday' => 'Dienstag',
                                'wednesday' => 'Mittwoch',
                                'thursday' => 'Donnerstag',
                                'friday' => 'Freitag',
                                'saturday' => 'Samstag',
                                'sunday' => 'Sonntag',
                            ];
                        @endphp

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <tbody>
                                @foreach($days as $key => $label)
                                    @php
                                        $o = data_get($opening, "{$key}.open");
                                        $c = data_get($opening, "{$key}.close");
                                        $closed = empty($o) || empty($c);
                                    @endphp
                                    <tr>
                                        <td class="text-muted" style="width: 40%;">{{ $label }}</td>
                                        <td class="fw-semibold">
                                            @if($closed)
                                                <span class="text-muted">geschlossen / keine Daten</span>
                                            @else
                                                {{ $o }} – {{ $c }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">Keine Öffnungszeiten hinterlegt.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Wartezeiten (Queue-Times) --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">⏱️ Wartezeiten</h2>

                    @php
                        $waiting = $waiting_times ?? null;   // wenn Controller es liefert
                        $lastUpdated = $last_updated ?? null;
                    @endphp

                    @if(is_array($waiting) && count($waiting))
                        @if($lastUpdated)
                            <div class="small text-muted mb-2">Letztes Update: {{ $lastUpdated }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>Attraktion</th>
                                        <th class="text-end">Wartezeit</th>
                                        <th class="text-end">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach(array_slice($waiting, 0, 25) as $ride)
                                    <tr>
                                        <td class="fw-semibold">{{ data_get($ride,'name') }}</td>
                                        <td class="text-end">
                                            @php $wt = data_get($ride,'waitingtime'); @endphp
                                            @if($wt === null)
                                                <span class="text-muted">–</span>
                                            @else
                                                {{ (int)$wt }} min
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @php $st = data_get($ride,'status'); @endphp
                                            @if($st === 'opened')
                                                <span class="badge bg-success-subtle text-success">offen</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">zu</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted">
                            Keine Live-Wartezeiten verfügbar.
                            @if(!empty($park->queue_times_id))
                                <div class="small mt-1">QueueTimes-ID: {{ $park->queue_times_id }}</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Video --}}
        @if($hasVideo)
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3">🎥 Video</h2>

                        @if(!empty($park->embed_code))
                            <div class="ratio ratio-16x9">
                                {!! $park->embed_code !!}
                            </div>
                        @elseif(!empty($park->video_url))
                            @if(str_contains($park->video_url, 'youtube.com'))
                                <div class="ratio ratio-16x9">
                                    <iframe
                                        src="{{ str_replace('watch?v=', 'embed/', $park->video_url) }}"
                                        frameborder="0"
                                        allowfullscreen
                                    ></iframe>
                                </div>
                            @else
                                <a href="{{ $park->video_url }}" target="_blank" rel="nofollow noopener" class="btn btn-outline-secondary">
                                    Video öffnen
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

<style>
.park-show-card { border-radius: 18px; }
.park-show-logo {
    background: #f8f9fa;
    padding: 18px;
    display:flex;
    align-items:center;
    justify-content:center;
}
.park-show-logo img {
    max-height: 220px;
    object-fit: contain;
}
</style>
@endsection
