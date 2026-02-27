<div class="container-fluid py-4">
    @php $parks = $parks ?? collect(); @endphp
    {{-- HERO / HEADER --}}
    <div class="container mb-4">
        <div
            class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
            <div>
                <h2 class="fw-bold mb-1">
                    @if (($total ?? 0) > 0)
                        {{ $total }} Reiseziele entdecken
                    @else
                        Reiseziele entdecken
                    @endif
                </h2>
                <div class="text-muted">
                    @if (!empty($month))
                        Für <strong>{{ config('custom.months')[$month] ?? 'deinen Monat' }}</strong>
                    @else
                        Wähle Filter und lass dich inspirieren
                    @endif
                    — Bilder vorne, Daten im Detail. 🙂
                </div>
            </div>

            {{-- MODE TOGGLE --}}
            <div class="btn-group">
                <button type="button" wire:click="$set('mode','inspiration')"
                    class="btn {{ $mode === 'inspiration' ? 'btn-primary' : 'btn-outline-primary' }}">
                    ✨ Inspiration
                </button>
                <button type="button" wire:click="$set('mode','detail')"
                    class="btn {{ $mode === 'detail' ? 'btn-primary' : 'btn-outline-primary' }}">
                    🔎 Detailsuche
                </button>
            </div>
        </div>
    </div>

    @php
        $continentName = $continent ? \App\Models\WwdeContinent::where('id', $continent)->value('title') : null;

        $monthName = $month ? config('custom.months')[$month] ?? null : null;

        // optionale Übersetzungen für Activities (wenn du eine Map hast)
        $activityLabels = [
            'list_beach' => '☀ Strand',
            'list_citytravel' => '🏙 Städtereise',
            'list_nature' => '🌿 Natur',
            'list_sports' => '⚽ Sport',
            'list_island' => '🏝 Insel',
            'list_culture' => '🏛 Kultur',
            'list_wintersport' => '🎿 Wintersport',
            'list_amusement_park' => '🎢 Mit Freizeitpark',
            'list_water_park' => '🌊 Mit Wasserpark',
            'list_animal_park' => '🦁 Mit Tierpark',
        ];
    @endphp

    {{-- ACTIVE FILTER CHIPS --}}
    @if (
        $continent ||
            $country ||
            $month ||
            $price ||
            $sunshine_min ||
            $water_temp_min ||
            $nurInBesterReisezeit ||
            $daily_temp_min ||
            $daily_temp_max ||
            $flight_duration ||
            $distance ||
            $language ||
            $currency ||
            ($visum !== null && $visum !== '') ||
            $price_tendency ||
            !empty($activities) ||
            !empty($tags))
        <div class="container mb-3">
            <div class="d-flex flex-wrap align-items-center gap-2 p-2 bg-light rounded">

                <span class="text-muted small me-1">Aktive Filter:</span>

                @if ($continent)
                    <button type="button" class="btn btn-sm btn-light border" wire:click="removeFilter('continent')">
                        🌍 {{ $continentName ?? 'Kontinent' }}
                        <span class="ms-1">✕</span>
                    </button>
                @endif

                @if ($month)
                    <button type="button" class="btn btn-sm btn-light border" wire:click="removeFilter('month')">
                        📅 {{ $monthName ?? 'Monat' }}
                        <span class="ms-1">✕</span>
                    </button>
                @endif

                @if ($nurInBesterReisezeit)
                    <button type="button" class="btn btn-sm btn-light border"
                        wire:click="removeFilter('nurInBesterReisezeit')">
                        ⭐ Beste Reisezeit
                        <span class="ms-1">✕</span>
                    </button>
                @endif

                @if ($sunshine_min)
                    <button type="button" class="btn btn-sm btn-light border"
                        wire:click="removeFilter('sunshine_min')">
                        ☀ ≥ {{ (int) $sunshine_min }}h
                        <span class="ms-1">✕</span>
                    </button>
                @endif

                @if ($water_temp_min)
                    <button type="button" class="btn btn-sm btn-light border"
                        wire:click="removeFilter('water_temp_min')">
                        🌊 ≥ {{ (int) $water_temp_min }}°C
                        <span class="ms-1">✕</span>
                    </button>
                @endif

                @if ($daily_temp_min)
                    <button type="button" class="btn btn-sm btn-light border"
                        wire:click="removeFilter('daily_temp_min')">
                        🌡 Tag ≥ {{ (int) $daily_temp_min }}°C
                        <span class="ms-1">✕</span>
                    </button>
                @endif

                @if ($daily_temp_max)
                    <button type="button" class="btn btn-sm btn-light border"
                        wire:click="removeFilter('daily_temp_max')">
                        🌡 Tag ≤ {{ (int) $daily_temp_max }}°C
                        <span class="ms-1">✕</span>
                    </button>
                @endif

                @if ($flight_duration)
                    <button type="button" class="btn btn-sm btn-light border"
                        wire:click="removeFilter('flight_duration')">
                        ✈ ≤ {{ (int) $flight_duration }}h
                        <span class="ms-1">✕</span>
                    </button>
                @endif

                @if ($distance)
                    <button type="button" class="btn btn-sm btn-light border" wire:click="removeFilter('distance')">
                        🧭 ≤ {{ number_format((int) $distance, 0, ',', '.') }} km
                        <span class="ms-1">✕</span>
                    </button>
                @endif

                @if ($language)
                    <button type="button" class="btn btn-sm btn-light border" wire:click="removeFilter('language')">
                        🗣 {{ strtoupper($language) }}
                        <span class="ms-1">✕</span>
                    </button>
                @endif

                @if ($currency)
                    <button type="button" class="btn btn-sm btn-light border" wire:click="removeFilter('currency')">
                        💱 {{ strtoupper($currency) }}
                        <span class="ms-1">✕</span>
                    </button>
                @endif

                @if ($visum !== null && $visum !== '')
                    <button type="button" class="btn btn-sm btn-light border" wire:click="removeFilter('visum')">
                        🛂 {{ (int) $visum === 1 ? 'Visum nötig' : 'Kein Visum' }}
                        <span class="ms-1">✕</span>
                    </button>
                @endif

                @if ($price_tendency)
                    <button type="button" class="btn btn-sm btn-light border"
                        wire:click="removeFilter('price_tendency')">
                        💶 {{ $price_tendency }}
                        <span class="ms-1">✕</span>
                    </button>
                @endif

                {{-- Activities --}}
                @if (!empty($activities))
                    @foreach ((array) $activities as $act)
                        <button type="button" class="btn btn-sm btn-light border"
                            wire:click="removeFilter('activities', '{{ $act }}')">
                            🏷 {{ $activityLabels[$act] ?? $act }}
                            <span class="ms-1">✕</span>
                        </button>
                    @endforeach
                @endif

                {{-- TAG CHIPS --}}
                @if (!empty($tags))
                    @foreach ($tags as $group => $slugs)
                        @foreach ((array) $slugs as $slug)
                            @php
                                $lookupKey = $group . '.' . $slug;
                                $tag = $tagLookup[$lookupKey] ?? null;
                            @endphp

                            @if ($tag)
                                <button type="button" class="btn btn-sm btn-light border"
                                    wire:click="removeFilter('tags', '{{ json_encode(['group' => $group, 'slug' => $slug]) }}')">
                                    🏷 {{ $tag['title'] }}
                                    <span class="ms-1">✕</span>
                                </button>
                            @endif
                        @endforeach
                    @endforeach
                @endif

                <div class="ms-auto">
                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="resetAllFilters">
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
                        <select wire:model.live="continent" class="form-select">
                            <option value="">Alle</option>
                            @foreach (\App\Models\WwdeContinent::select('id', 'title')->orderBy('title')->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->title }}</option>
                            @endforeach
                        </select>

                        @if ($continent)
                            <div class="mt-3">
                                <label class="form-label">Land</label>
                                <select wire:model.defer="country" class="form-select">
                                    <option value="">Alle Länder</option>
                                    @foreach ($this->countries as $c)
                                        <option value="{{ $c->id }}">
                                            {{ $c->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif




                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reisemonat</label>
                        <select wire:model.change="month" class="form-select">
                            <option value="">Beliebig</option>
                            @foreach (config('custom.months') as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" wire:model.defer="nurInBesterReisezeit"
                            id="bestTimeOnly">
                        <label class="form-check-label" for="bestTimeOnly">
                            Nur „beste Reisezeit“ zeigen
                        </label>
                    </div>


                    <hr>

                    <h6 class="fw-bold mb-3">✨ Reiseart</h6>

                    @php
                        $motivOptions = [
                            'list_beach' => '☀ Strand',
                            'list_citytravel' => '🏙 Städtereise',
                            'list_nature' => '🌿 Natur',
                            'list_sports' => '⚽ Sport',
                            'list_island' => '🏝 Insel',
                            'list_culture' => '🏛 Kultur',
                            'list_wintersport' => '🎿 Wintersport',
                            'list_amusement_park' => '🎢 Mit Freizeitpark',
                        ];
                    @endphp

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @foreach ($motivOptions as $field => $label)
                            <label class="motiv-chip">
                                <input type="checkbox" value="{{ $field }}" wire:model.defer="activities"
                                    hidden>
                                <span class="chip-label">
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach
                    </div>


                    {{-- 🌞 Sonnenstunden --}}
                    <div class="premium-slider mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-semibold mb-0">Min. Sonnenstunden</label>

                            @if ($sunshine_min !== null)
                                <span class="slider-value">
                                    {{ $sunshine_min }}h
                                </span>
                            @endif
                        </div>

                        <input type="range" min="0" max="12" step="1"
                            wire:model.live="sunshine_min" class="range-slider sun"
                            style="--progress: {{ $this->sunPercent }}%;" @disabled(!$month)>

                        <small class="text-muted">
                            Wirkt nur zusammen mit Monat.
                        </small>
                    </div>


                    {{-- 🌊 Wassertemperatur --}}
                    <div class="premium-slider mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-semibold mb-0">Min. Wassertemperatur</label>

                            @if ($water_temp_min !== null)
                                <span class="slider-value">
                                    {{ $water_temp_min }}°C
                                </span>
                            @endif
                        </div>

                        <input type="range" min="0" max="35" step="1"
                            wire:model.live="water_temp_min" class="range-slider water"
                            style="--progress: {{ $this->waterPercent }}%;" @disabled(!$month)>

                        <small class="text-muted">
                            Wirkt nur zusammen mit Monat.
                        </small>
                    </div>

                    <hr>

                    @foreach ($availableTagGroups as $groupKey => $groupData)
                        @php
                            $collapseId = 'collapse_' . $groupKey;
                            $label = ucfirst($groupData['label']);
                        @endphp

                        <div class="mb-3">

                            {{-- Header --}}
                            <button
                                class="filter-collapse-btn w-100 d-flex justify-content-between align-items-center mb-2"
                                type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}"
                                aria-expanded="{{ !empty($tags[$groupKey]) ? 'true' : 'false' }}"
                                aria-controls="{{ $collapseId }}">

                                <span class="fw-semibold">
                                    {{ $label }}
                                </span>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="small text-muted">
                                        {{ count($groupData['items']) }}
                                    </span>

                                    <span class="chevron">
                                        <i class="bi bi-chevron-down chevron"></i>
                                    </span>
                                </div>
                            </button>

                            {{-- Collapsible Body --}}
                            <div class="collapse {{ !empty($tags[$groupKey]) ? 'show' : '' }}"
                                id="{{ $collapseId }}">

                                @foreach ($groupData['items'] as $tag)
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" value="{{ $tag['slug'] }}"
                                            wire:model.defer="tags.{{ $groupKey }}"
                                            id="tag_{{ $groupKey }}_{{ $tag['slug'] }}">

                                        <label class="form-check-label"
                                            for="tag_{{ $groupKey }}_{{ $tag['slug'] }}">
                                            {{ $tag['title'] }}
                                        </label>
                                    </div>
                                @endforeach

                            </div>

                        </div>
                    @endforeach



                    {{-- DETAIL FILTERS (nur im Detail-Modus) --}}
                    @if ($mode === 'detail')
                        <hr>

                        <h6 class="fw-bold mb-3">Feintuning</h6>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">Tag min</label>
                                <input type="number" wire:model.defer="daily_temp_min" class="form-control"
                                    placeholder="z.B. 20">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Tag max</label>
                                <input type="number" wire:model.defer="daily_temp_max" class="form-control"
                                    placeholder="z.B. 30">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Flugdauer bis (h)</label>
                            <input type="number" wire:model.defer="flight_duration" class="form-control"
                                placeholder="z.B. 5">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Distanz bis (km)</label>
                            <input type="number" wire:model.defer="distance" class="form-control"
                                placeholder="z.B. 3000">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sprache</label>
                            <input type="text" wire:model.defer="language" class="form-control"
                                placeholder="z.B. en">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Währung</label>
                            <input type="text" wire:model.defer="currency" class="form-control"
                                placeholder="z.B. EUR">
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
                            <input type="text" wire:model.defer="price_tendency" class="form-control"
                                placeholder="z.B. günstig">
                        </div>
                    @endif

                    <button wire:click="applyFilters" wire:loading.attr="disabled"
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

{{-- =========================
     TOP REISEARTEN
========================= --}}

@php
$topReisearten = [
    'list_beach' => ['icon' => '☀', 'label' => 'Strandurlaub'],
    'list_citytravel' => ['icon' => '🏙', 'label' => 'Städtereise'],
    'list_nature' => ['icon' => '🌿', 'label' => 'Natur & Erholung'],
    'list_island' => ['icon' => '🏝', 'label' => 'Inselurlaub'],
];
@endphp

<div class="mb-4">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="small text-muted">
            Beliebte Reisearten
        </div>

        @if(!empty($activities))
            <button class="btn btn-sm btn-link text-muted p-0"
                    wire:click="$set('activities', [])">
                Zurücksetzen
            </button>
        @endif
    </div>

    <div class="d-flex flex-wrap gap-2">

        @foreach($topReisearten as $field => $data)

            @php
                $active = in_array($field, (array)$activities);
            @endphp

            <button
                type="button"
                wire:click="toggleReiseart('{{ $field }}')"
                class="reiseart-btn {{ $active ? 'active' : '' }}"
            >
                <span class="me-1">{{ $data['icon'] }}</span>
                {{ $data['label'] }}
            </button>

        @endforeach

    </div>

</div>


                {{-- Toolbar --}}
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">


@if(($total ?? 0) > 0)

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">

    <div class="text-muted">
        <strong>{{ $total }}</strong> Reiseziele gefunden
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="text-muted small">Sortieren nach</span>
        <select wire:model.live="sortBy"
                class="form-select form-select-sm w-auto">
            <option value="match">⭐ Beste Übereinstimmung</option>
            <option value="price_flight">💶 Günstigster Preis</option>
            <option value="title">🔤 Alphabetisch</option>
        </select>
    </div>

</div>

@endif
                </div>

                {{-- INSPIRATION GRID --}}
                <div class="row g-4">
                    @forelse($results as $location)
                        @php
                            $img = $location->primaryImage();
                            $country = $location->country->title ?? '';
                            $score = $location->match_score ?? null;
                            $sun = data_get($location, 'climate_data.sunshine_hours');
                            $water = data_get($location, 'climate_data.water_temperature');
                            $rain = data_get($location, 'climate_data.rainy_days');

                            $price = (int) $location->price_flight;
                            $priceFormatted = number_format($price, 0, ',', '.');
                        @endphp

                        <div class="col-md-6 col-xl-4 mb-4">
                            <div class="card search-card h-100 border-0 shadow-sm rounded-3 overflow-hidden">

                                <div class="search-card-image position-relative">
                                    @if ($img)
                                        <img src="{{ asset($img) }}" class="w-100 h-100"
                                            style="object-fit: cover;" alt="{{ $location->title }}" loading="lazy">
                                    @endif
                                </div>

                                <div class="card-body p-3">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small text-primary">{{ $country }}</span>

                                        @if ($score !== null)
                                            <span class="search-card-match">
                                                {{ $score }}% Match
                                            </span>
                                        @endif
                                    </div>

                                    <div class="search-card-title mb-2">
                                        {{ $location->title }}
                                    </div>

                                    <div class="search-card-meta d-flex gap-3 mb-2">
                                        @if ($sun !== null)
                                            <span>☀️ {{ $sun }}h</span>
                                        @endif
                                        @if ($water !== null)
                                            <span>🌊 {{ (int) $water }}°C</span>
                                        @endif
                                        @if ($location->flight_hours)
                                            <span>✈️ {{ number_format($location->flight_hours, 1) }}h</span>
                                        @endif
                                    </div>

                                    <div class="search-card-footer d-flex justify-content-between align-items-center">
                                        <span class="search-card-price-label">Flug ab</span>
                                        <span class="search-card-price">{{ $priceFormatted }} €</span>
                                    </div>
                                </div>

                                <a href="/reise/{{ $location->full_slug }}" class="stretched-link"></a>
                            </div>
                        </div>
@empty
<div class="col-12">
    <div class="text-center py-5">

        <div style="font-size:40px;">🔍</div>

        <h5 class="mt-3 fw-semibold">
            Keine passenden Reiseziele gefunden
        </h5>

        <p class="text-muted mb-4">
            Deine aktuelle Filterkombination ist zu restriktiv.
        </p>

        <div class="d-flex justify-content-center flex-wrap gap-2">

            @if($sunshine_min)
            <button wire:click="$set('sunshine_min', null)"
                    class="btn btn-outline-secondary btn-sm">
                ☀ Sonnenstunden entfernen
            </button>
            @endif

            @if($water_temp_min)
            <button wire:click="$set('water_temp_min', null)"
                    class="btn btn-outline-secondary btn-sm">
                🌊 Wassertemperatur entfernen
            </button>
            @endif

            <button wire:click="resetAllFilters"
                    class="btn btn-primary btn-sm">
                Alle Filter zurücksetzen
            </button>

        </div>

    </div>
</div>
@endforelse
                </div>

                @if (isset($parks) && $parks->isNotEmpty() && $total > 0)
                    <div class="mt-5">
                        <h3 class="mt-5 mb-3">🎢 Passende Freizeitparks in der Region</h3>
                        @foreach ($parks as $park)
                            <x-search.park-card :park="$park" />
                        @endforeach
                    </div>
                @endif


                {{-- Pagination --}}
                @if ($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-4">
                        {{ $results->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    <style>
        /* ==============================
   SEARCH CARD DESIGN SYSTEM
   ============================== */

        .search-card {
            transition: all 0.25s ease;
            border-radius: 14px;
            background: #ffffff;
            position: relative;
        }

        .search-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        /* Bildbereich */
        .search-card-image {
            height: 210px;
            overflow: hidden;
            background: #f1f3f5;
        }

        .search-card-image img {
            transition: transform 0.35s ease;
        }

        .search-card:hover .search-card-image img {
            transform: scale(1.05);
        }

        /* Match Badge */
        .search-card-match {
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 20px;
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
            font-weight: 600;
        }

        /* Titel */
        .search-card-title {
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1.2;
            min-height: 38px;
        }

        /* Meta Infos */
        .search-card-meta {
            font-size: 0.82rem;
            color: #6c757d;
        }

        /* Preisbereich */
        .search-card-price-label {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .search-card-price {
            font-size: 1.05rem;
            font-weight: 700;
            color: #111;
        }

        /* Border Top Trennung */
        .search-card-footer {
            border-top: 1px solid #f0f0f0;
            padding-top: 10px;
            margin-top: 10px;
        }

        /* Card Grid Spacing Feinschliff */
        @media (min-width: 1200px) {
            .col-xl-4 {
                margin-bottom: 1.5rem;
            }
        }

        /* Sidebar Collapse Button cleaner */
        .filter-collapse-btn {
            font-size: 0.9rem;
            font-weight: 600;
            color: #212529;
            background: none;
            border: none;
        }

        .filter-collapse-btn:hover {
            color: #0d6efd;
        }

        /* Active filter chips */
        .active-filter-chip {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }

        .active-filter-chip:hover {
            background: #e9ecef;
        }

        /* =========================
   PREMIUM LIVEWIRE SLIDER
   ========================= */

        .range-slider {
            width: 100%;
            height: 6px;
            border-radius: 6px;
            appearance: none;
            cursor: pointer;
            background:
                linear-gradient(to right,
                    var(--slider-color) 0%,
                    var(--slider-color) var(--progress),
                    #e9ecef var(--progress),
                    #e9ecef 100%);
        }

        /* Sonnenfarbe */
        .range-slider.sun {
            --slider-color: #ffb703;
        }

        /* Wasserfarbe */
        .range-slider.water {
            --slider-color: #0dcaf0;
        }

        /* Thumb */
        .range-slider::-webkit-slider-thumb {
            appearance: none;
            height: 18px;
            width: 18px;
            border-radius: 50%;
            background: white;
            border: 3px solid var(--slider-color);
            margin-top: -6px;
            transition: 0.2s ease;
        }

        .range-slider::-webkit-slider-thumb:hover {
            transform: scale(1.15);
        }

        .range-slider::-moz-range-thumb {
            height: 18px;
            width: 18px;
            border-radius: 50%;
            background: white;
            border: 3px solid var(--slider-color);
        }

        /* Disabled */
        .range-slider:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .slider-value {
            font-weight: 600;
        }

        /* Collapse Chevron */

        .filter-collapse-btn {
            background: none;
            border: none;
            padding: 0;
        }

        .filter-collapse-btn .chevron {
            display: inline-block;
            transition: transform 0.25s ease;
            font-size: 0.8rem;
        }

        /* Wenn geöffnet → drehen */
        .filter-collapse-btn[aria-expanded="true"] .chevron {
            transform: rotate(180deg);
        }

        /* =========================
   MOTIV FILTER CHIPS
   ========================= */

        .motiv-chip {
            cursor: pointer;
        }

        .motiv-chip input:checked+.chip-label {
            background: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }

        .chip-label {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            border: 1px solid #dee2e6;
            font-size: 0.85rem;
            background: #f8f9fa;
            transition: all 0.2s ease;
        }

        .chip-label:hover {
            background: #e9ecef;
        }

        /* =========================
   TOP REISEARTEN BUTTONS
========================= */

.reiseart-btn {
    border: none;
    background: #f1f3f5;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    transition: all 0.2s ease;
}

.reiseart-btn:hover {
    background: #e9ecef;
}

.reiseart-btn.active {
    background: #212529;
    color: white;
}
    </style>

    <script>
        function updateSliderFill(slider) {
            const min = slider.min ? slider.min : 0;
            const max = slider.max ? slider.max : 100;
            const val = slider.value;

            const percent = ((val - min) * 100) / (max - min);

            slider.style.setProperty('--fill-percent', percent + '%');

            if (slider.classList.contains('range-sun')) {
                slider.style.setProperty('--fill-color', '#ffb703');
            }

            if (slider.classList.contains('range-water')) {
                slider.style.setProperty('--fill-color', '#0dcaf0');
            }
        }

        document.querySelectorAll('.range-slider').forEach(slider => {
            updateSliderFill(slider);
        });
    </script>
</div>
