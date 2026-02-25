<div class="container-fluid py-4">

    {{-- HERO / HEADER --}}
    <div class="container mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
            <div>
                <h2 class="fw-bold mb-1">
                    @if(($total ?? 0) > 0)
                        {{ $total }} Reiseziele entdecken
                    @else
                        Reiseziele entdecken
                    @endif
                </h2>
                <div class="text-muted">
                    @if(!empty($month))
                        Für <strong>{{ config('custom.months')[$month] ?? 'deinen Monat' }}</strong>
                    @else
                        Wähle Filter und lass dich inspirieren
                    @endif
                    — Bilder vorne, Daten im Detail. 🙂
                </div>
            </div>

            {{-- MODE TOGGLE --}}
            <div class="btn-group">
                <button type="button"
                        wire:click="$set('mode','inspiration')"
                        class="btn {{ $mode === 'inspiration' ? 'btn-primary' : 'btn-outline-primary' }}">
                    ✨ Inspiration
                </button>
                <button type="button"
                        wire:click="$set('mode','detail')"
                        class="btn {{ $mode === 'detail' ? 'btn-primary' : 'btn-outline-primary' }}">
                    🔎 Detailsuche
                </button>
            </div>
        </div>
    </div>

@php
    $continentName = $continent
        ? \App\Models\WwdeContinent::where('id', $continent)->value('title')
        : null;

    $monthName = $month
        ? (config('custom.months')[$month] ?? null)
        : null;

    // optionale Übersetzungen für Activities (wenn du eine Map hast)
    $activityLabels = [
        'list_beach' => 'Strand',
        'list_citytravel' => 'Stadt',
        'list_sports' => 'Sport',
        'list_island' => 'Insel',
        'list_culture' => 'Kultur',
        'list_nature' => 'Natur',
    ];
@endphp

{{-- ACTIVE FILTER CHIPS --}}
@if(
    $continent || $country || $month || $price ||
    $sunshine_min || $water_temp_min || $nurInBesterReisezeit ||
    $daily_temp_min || $daily_temp_max || $flight_duration || $distance ||
    $language || $currency || ($visum !== null && $visum !== '') || $price_tendency ||
    (!empty($activities))
)
    <div class="container mb-3">
        <div class="d-flex flex-wrap align-items-center gap-2">

            <span class="text-muted small me-1">Aktive Filter:</span>

            @if($continent)
                <button type="button"
                        class="btn btn-sm btn-light border"
                        wire:click="removeFilter('continent')">
                    🌍 {{ $continentName ?? 'Kontinent' }}
                    <span class="ms-1">✕</span>
                </button>
            @endif

            @if($month)
                <button type="button"
                        class="btn btn-sm btn-light border"
                        wire:click="removeFilter('month')">
                    📅 {{ $monthName ?? 'Monat' }}
                    <span class="ms-1">✕</span>
                </button>
            @endif

            @if($nurInBesterReisezeit)
                <button type="button"
                        class="btn btn-sm btn-light border"
                        wire:click="removeFilter('nurInBesterReisezeit')">
                    ⭐ Beste Reisezeit
                    <span class="ms-1">✕</span>
                </button>
            @endif

            @if($sunshine_min)
                <button type="button"
                        class="btn btn-sm btn-light border"
                        wire:click="removeFilter('sunshine_min')">
                    ☀ ≥ {{ (int)$sunshine_min }}h
                    <span class="ms-1">✕</span>
                </button>
            @endif

            @if($water_temp_min)
                <button type="button"
                        class="btn btn-sm btn-light border"
                        wire:click="removeFilter('water_temp_min')">
                    🌊 ≥ {{ (int)$water_temp_min }}°C
                    <span class="ms-1">✕</span>
                </button>
            @endif

            @if($daily_temp_min)
                <button type="button"
                        class="btn btn-sm btn-light border"
                        wire:click="removeFilter('daily_temp_min')">
                    🌡 Tag ≥ {{ (int)$daily_temp_min }}°C
                    <span class="ms-1">✕</span>
                </button>
            @endif

            @if($daily_temp_max)
                <button type="button"
                        class="btn btn-sm btn-light border"
                        wire:click="removeFilter('daily_temp_max')">
                    🌡 Tag ≤ {{ (int)$daily_temp_max }}°C
                    <span class="ms-1">✕</span>
                </button>
            @endif

            @if($flight_duration)
                <button type="button"
                        class="btn btn-sm btn-light border"
                        wire:click="removeFilter('flight_duration')">
                    ✈ ≤ {{ (int)$flight_duration }}h
                    <span class="ms-1">✕</span>
                </button>
            @endif

            @if($distance)
                <button type="button"
                        class="btn btn-sm btn-light border"
                        wire:click="removeFilter('distance')">
                    🧭 ≤ {{ number_format((int)$distance, 0, ',', '.') }} km
                    <span class="ms-1">✕</span>
                </button>
            @endif

            @if($language)
                <button type="button"
                        class="btn btn-sm btn-light border"
                        wire:click="removeFilter('language')">
                    🗣 {{ strtoupper($language) }}
                    <span class="ms-1">✕</span>
                </button>
            @endif

            @if($currency)
                <button type="button"
                        class="btn btn-sm btn-light border"
                        wire:click="removeFilter('currency')">
                    💱 {{ strtoupper($currency) }}
                    <span class="ms-1">✕</span>
                </button>
            @endif

            @if(($visum !== null && $visum !== ''))
                <button type="button"
                        class="btn btn-sm btn-light border"
                        wire:click="removeFilter('visum')">
                    🛂 {{ (int)$visum === 1 ? 'Visum nötig' : 'Kein Visum' }}
                    <span class="ms-1">✕</span>
                </button>
            @endif

            @if($price_tendency)
                <button type="button"
                        class="btn btn-sm btn-light border"
                        wire:click="removeFilter('price_tendency')">
                    💶 {{ $price_tendency }}
                    <span class="ms-1">✕</span>
                </button>
            @endif

            @if(!empty($activities))
                @foreach((array)$activities as $act)
                    <button type="button"
                            class="btn btn-sm btn-light border"
                            wire:click="removeFilter('activities', '{{ $act }}')">
                        🏷 {{ $activityLabels[$act] ?? $act }}
                        <span class="ms-1">✕</span>
                    </button>
                @endforeach
            @endif

            <div class="ms-auto">
                <button type="button"
                        class="btn btn-sm btn-outline-secondary"
                        wire:click="resetAllFilters">
                    Alles zurücksetzen
                </button>
            </div>

        </div>
    </div>
@endif



    <div class="container">
        <div class="row">

            {{-- SIDEBAR --}}
            <div class="col-lg-3 mb-4">

                <div class="card shadow-sm p-3 sticky-top" style="top: 90px;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold mb-0">Filter</h5>
                        <span class="badge bg-light text-muted">
                            {{ $mode === 'detail' ? 'Pro' : 'Easy' }}
                        </span>
                    </div>

                    {{-- BASIC FILTERS --}}
                    <div class="mb-3">
                        <label class="form-label">Kontinent</label>
                        <select wire:model.defer="continent" class="form-select">
                            <option value="">Alle</option>
                            @foreach(\App\Models\WwdeContinent::select('id','title')->orderBy('title')->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reisemonat</label>
                        <select wire:model.defer="month" class="form-select">
                            <option value="">Beliebig</option>
                            @foreach(config('custom.months') as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input"
                               type="checkbox"
                               wire:model.defer="nurInBesterReisezeit"
                               id="bestTimeOnly">
                        <label class="form-check-label" for="bestTimeOnly">
                            Nur „beste Reisezeit“ zeigen
                        </label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Min. Sonnenstunden</label>
                        <input type="number" wire:model.defer="sunshine_min" class="form-control" placeholder="z.B. 7">
                        <div class="form-text">Wirkt nur zusammen mit Monat.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Min. Wassertemperatur</label>
                        <input type="number" wire:model.defer="water_temp_min" class="form-control" placeholder="z.B. 22">
                        <div class="form-text">Wirkt nur zusammen mit Monat.</div>
                    </div>

                    {{-- DETAIL FILTERS (nur im Detail-Modus) --}}
                    @if($mode === 'detail')
                        <hr>

                        <h6 class="fw-bold mb-3">Feintuning</h6>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">Tag min</label>
                                <input type="number" wire:model.defer="daily_temp_min" class="form-control" placeholder="z.B. 20">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Tag max</label>
                                <input type="number" wire:model.defer="daily_temp_max" class="form-control" placeholder="z.B. 30">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Flugdauer bis (h)</label>
                            <input type="number" wire:model.defer="flight_duration" class="form-control" placeholder="z.B. 5">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Distanz bis (km)</label>
                            <input type="number" wire:model.defer="distance" class="form-control" placeholder="z.B. 3000">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sprache</label>
                            <input type="text" wire:model.defer="language" class="form-control" placeholder="z.B. en">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Währung</label>
                            <input type="text" wire:model.defer="currency" class="form-control" placeholder="z.B. EUR">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Visum</label>
                            <select wire:model.defer="visum" class="form-select">
                                <option value="">egal</option>
                                <option value="0">kein Visum nötig</option>
                                <option value="1">Visum nötig</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Preistendenz</label>
                            <input type="text" wire:model.defer="price_tendency" class="form-control" placeholder="z.B. günstig">
                        </div>
                    @endif

                    <button wire:click="applyFilters"
                            wire:loading.attr="disabled"
                            class="btn btn-primary w-100 fw-semibold mt-2">
                        <span wire:loading.remove>✨ Inspiration laden</span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm"></span>
                            Suche läuft...
                        </span>
                    </button>

                    <div class="text-muted small mt-2">
                        Filter werden erst beim Button angewendet (ruhig & schnell).
                    </div>
                </div>

            </div>


            {{-- RESULTS --}}
            <div class="col-lg-9">

                {{-- Toolbar --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
                    <div class="text-muted">
                        @if(($total ?? 0) > 0)
                            <strong>{{ $total }}</strong> Ergebnisse
                        @else
                            Keine Ergebnisse (oder noch nicht gesucht)
                        @endif
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Sortierung</span>
                        <select wire:model="sortBy" class="form-select form-select-sm w-auto">
                            <option value="match">Empfohlen</option>
                            <option value="title">Name</option>
                            <option value="price_flight">Preis</option>
                        </select>
                    </div>
                </div>

                {{-- INSPIRATION GRID --}}
                <div class="row">
                    @forelse($results as $location)

                        @php
                            // optional: du enrichst bereits climate_data in alter Suche.
                            // hier erwarten wir (wenn vorhanden): $location->climate_data['sunshine_hours'], ['water_temperature'], ['rainy_days']
                            $img = $location->primaryImage();
                            $country = $location->country->title ?? '';
                            $score = $location->match_score ?? null;
                            $sun = data_get($location, 'climate_data.sunshine_hours');
                            $water = data_get($location, 'climate_data.water_temperature');
                            $rain = data_get($location, 'climate_data.rainy_days');
                        @endphp

                        <div class="col-md-6 col-xl-4 mb-4">
                            <div class="card h-100 shadow-sm border-0 overflow-hidden">

                                {{-- Image --}}
                                <div style="height: 210px; background: #e9ecef;">
                                    @if($img)
                                        <img src="{{ asset($img) }}"
                                             class="w-100 h-100"
                                             style="object-fit: cover;"
                                             alt="{{ $location->title }}">
                                    @endif
                                </div>

                                {{-- Body --}}
                                <div class="card-body">

                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="text-muted small">{{ $country }}</div>

                                        {{-- Match subtil --}}
                                        @if($score !== null)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                Sehr passend · {{ $score }}%
                                            </span>
                                        @endif
                                    </div>

                                    <h5 class="fw-bold mb-2" style="line-height: 1.15;">
                                        {{ $location->title }}
                                    </h5>

                                    {{-- Mini-Facts (dateninformiert, aber nicht „vergleichsportal“) --}}
                                    <div class="d-flex flex-wrap gap-2 small text-muted">
                                        @if($sun !== null)
                                            <span>☀ {{ $sun }}h</span>
                                        @endif
                                        @if($water !== null)
                                            <span>🌊 {{ (int)$water }}°C</span>
                                        @endif
                                        @if($rain !== null)
                                            <span>🌧 {{ $rain }} Tage</span>
                                        @endif
                                        @if($location->flight_hours)
                                            <span>✈ {{ number_format($location->flight_hours, 1, ',', '.') }}h</span>
                                        @endif
                                    </div>

                                    {{-- Preis zurückhaltend --}}
                                    <div class="mt-3">
                                        <span class="text-muted small">Flug ab</span>
                                        <div class="fw-bold text-primary">
                                            {{ number_format((int)$location->price_flight, 0, ',', '.') }} €
                                        </div>
                                    </div>
                                </div>

                                {{-- Footer CTA --}}
                                <div class="card-footer bg-white border-0 pt-0">
                                    <a href="/reise/{{ $location->full_slug }}"
                                       class="btn btn-outline-primary w-100">
                                        Mehr entdecken
                                    </a>
                                </div>

                            </div>
                        </div>

                    @empty
                        <div class="col-12">
                            <div class="alert alert-light text-center">
                                Noch keine Suche gestartet – setz Filter und klick auf „Inspiration laden“.
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-4">
                        {{ $results->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
