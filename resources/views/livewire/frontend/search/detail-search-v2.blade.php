{{-- resources/views/livewire/frontend/search/detail-search-v2.blade.php --}}
<div>
    <div class="container py-4">
        <div class="row">
            <!-- Filter Sidebar -->
<div class="col-lg-3">

    {{-- 🔍 Suche --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <label class="form-label fw-semibold mb-2">
                <i class="fas fa-search me-1"></i> Reiseziel
            </label>

            <input
                type="text"
                wire:model.live="search"
                class="form-control"
                placeholder="Reiseziel suchen..."
            >
        </div>
    </div>

    {{-- ▶ Filter anwenden --}}
    <button
        wire:click="applyFilters"
        class="btn btn-warning w-100 mb-4 fw-semibold"
    >
        <i class="fas fa-filter me-2"></i>Ergebnisse anzeigen
    </button>

    {{-- 🌍 Kontinente --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white py-2">
            <strong>
                <i class="fas fa-globe me-2"></i>Kontinente
            </strong>
        </div>

        <div class="card-body p-2 filter-section" style="max-height:220px; overflow-y:auto;">
            @foreach($filterOptions['continents'] as $continent)
                <label
                    for="continent-{{ $continent->id }}"
                    class="d-flex align-items-center gap-2 px-2 py-1 rounded continent-row w-100"
                >
                    <input
                        type="checkbox"
                        id="continent-{{ $continent->id }}"
                        wire:model.defer="continents.{{ $continent->id }}"
                        class="form-check-input m-0"
                    >
                    <span class="small">{{ $continent->title }}</span>
                </label>
            @endforeach
        </div>
    </div>

<div class="card bg-light mb-4">
    <div class="card-header fw-semibold">
        Preise
    </div>

    <div class="card-body">
        <div class="row g-3">

            {{-- ✈ Flug --}}
            <div class="col-6 col-lg-12">
                <label class="d-flex align-items-center gap-2 small fw-semibold mb-1">
                    <i class="fas fa-plane-departure"></i> Flug
                </label>
                <select
                    wire:model.defer="price_flight"
                    class="form-select form-select-sm"
                >
                    <option value="">Beliebig</option>
                    @foreach($filterOptions['price_flight'] ?? [] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 🏨 Hotel --}}
            <div class="col-6 col-lg-12">
                <label class="d-flex align-items-center gap-2 small fw-semibold mb-1">
                    <i class="fas fa-bed"></i> Hotel
                </label>
                <select
                    wire:model.defer="price_hotel"
                    class="form-select form-select-sm"
                >
                    <option value="">Beliebig</option>
                    @foreach($filterOptions['price_hotel'] ?? [] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 🚗 Mietwagen --}}
            <div class="col-6 col-lg-12">
                <label class="d-flex align-items-center gap-2 small fw-semibold mb-1">
                    <i class="fas fa-car"></i> Mietwagen
                </label>
                <select
                    wire:model.defer="price_mietwagen"
                    class="form-select form-select-sm"
                >
                    <option value="">Beliebig</option>
                    @foreach($filterOptions['price_mietwagen'] ?? [] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- 🧳 Pauschalreise --}}
            <div class="col-6 col-lg-12">
                <label class="d-flex align-items-center gap-2 small fw-semibold mb-1">
                    <i class="fas fa-suitcase-rolling"></i> Pauschalreise
                </label>
                <select
                    wire:model.defer="price_pauschalreise"
                    class="form-select form-select-sm"
                >
                    <option value="">Beliebig</option>
                    @foreach($filterOptions['price_pauschalreise'] ?? [] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

        </div>
    </div>
</div>


<div class="card shadow-sm mb-4">
    <div class="card-header fw-semibold">
        Generelle Informationen
    </div>

    <div class="card-body">
        <div class="row g-3">

            {{-- Monat --}}
            <div class="col-6 col-lg-3">
                <label class="form-label small fw-semibold">
                    <i class="fas fa-calendar-alt me-1 text-warning"></i>
                    Monat
                </label>
                <select wire:model.defer="month" class="form-select form-select-sm">
                    <option value="">Beliebig</option>
                    @foreach(config('custom.months') as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Preis pro Person --}}
            <div class="col-6 col-lg-3">
                <label class="form-label small fw-semibold">
                    <i class="fas fa-suitcase-rolling me-1 text-warning"></i>
                    Preis pro Person
                </label>
                <select wire:model.defer="range_flight" class="form-select form-select-sm">
                    <option value="">Beliebig</option>
                    @foreach($filterOptions['price_flight'] ?? [] as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Land --}}
            <div class="col-6 col-lg-3">
                <label class="form-label small fw-semibold">
                    <i class="fas fa-globe me-1 text-warning"></i>
                    Land
                </label>
                <select wire:model.defer="country" class="form-select form-select-sm">
                    <option value="">Beliebig</option>
                    @foreach($filterOptions['countries'] ?? [] as $country)
                        <option value="{{ $country->id }}">{{ $country->title }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Klimazone --}}
            <div class="col-6 col-lg-3">
                <label class="form-label small fw-semibold">
                    <i class="fas fa-cloud-sun me-1 text-warning"></i>
                    Klimazone
                </label>
                <select wire:model.defer="climate_zone" class="form-select form-select-sm">
                    <option value="">Beliebig</option>
                    @foreach($filterOptions['climate_zones'] ?? [] as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

        </div>
    </div>
</div>



<div class="card shadow-sm mb-4">
    <div class="card-header fw-semibold bg-light">
        <i class="fas fa-info-circle me-2"></i>
        Generelle Informationen
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                {{-- Monat --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-calendar-alt me-1 text-primary"></i>
                        Monat
                    </label>
                    <select wire:model.live="month" class="form-select">
                        <option value="">Beliebig</option>
                        @foreach($filterOptions['months'] ?? config('custom.months', []) as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($month)
                        <small class="text-muted">Ausgewählt: {{ $filterOptions['months'][$month] ?? $month }}</small>
                    @endif
                </div>

                {{-- Preis pro Person --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-euro-sign me-1 text-primary"></i>
                        Preis pro Person
                    </label>
                    <select wire:model.live="price_range" class="form-select">
                        <option value="">Beliebig</option>
                        <option value="0-500">Bis 500 €</option>
                        <option value="500-1000">500 - 1.000 €</option>
                        <option value="1000-1500">1.000 - 1.500 €</option>
                        <option value="1500-2000">1.500 - 2.000 €</option>
                        <option value="2000-">Ab 2.000 €</option>
                    </select>
                </div>

                {{-- Preis-Tendenz --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-chart-line me-1 text-primary"></i>
                        Preis-Tendenz
                    </label>
                    <select wire:model.live="price_tendency" class="form-select">
                        <option value="">Beliebig</option>
                        @foreach($filterOptions['price_tendencies'] ?? [] as $key => $label)
                            @if($key !== '')
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                {{-- Land --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-flag me-1 text-primary"></i>
                        Land
                    </label>
                    <select wire:model.live="country" class="form-select">
                        <option value="">Beliebig</option>
                        @foreach($filterOptions['countries'] ?? [] as $country)
                            <option value="{{ $country->id }}">{{ $country->title }}</option>
                        @endforeach
                    </select>
                    @if($country)
                        <small class="text-muted">
                            Ausgewählt: {{ $filterOptions['countries']->firstWhere('id', $country)?->title }}
                        </small>
                    @endif
                </div>

                {{-- Klimazone --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-cloud-sun me-1 text-primary"></i>
                        Klimazone
                    </label>
                    <select wire:model.live="climate_zone" class="form-select">
                        <option value="">Beliebig</option>
                        @foreach($filterOptions['climate_zones'] ?? [] as $key => $label)
                            @if($key !== '')
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                {{-- Währung --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-money-bill-wave me-1 text-primary"></i>
                        Währung
                    </label>
                    <select wire:model.live="currency" class="form-select">
                        <option value="">Beliebig</option>
                        @foreach($filterOptions['currencies'] ?? [] as $key => $label)
                            @if($key !== '')
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>







    {{-- 🔧 Weitere Filter --}}
    @include('pages.detailSearch.v2.partials.filters', [
        'filterOptions' => $filterOptions,
        'wireModel' => true
    ])

    {{-- 🔄 Reset --}}
    <button
        wire:click="resetAllFilters"
        class="btn btn-outline-secondary w-100 mb-4"
    >
        <i class="fas fa-redo me-2"></i>Alle Filter zurücksetzen
    </button>

</div>



            <!-- Hauptinhalt -->
            <div class="col-lg-9">
                <!-- Header mit Stats -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="mb-2 mb-md-0">
                                <h4 class="mb-1">
                                    <span class="text-primary">{{ $totalCount }}</span>
                                    Reiseziele gefunden
                                </h4>
                                @if($search)
                                    <small class="text-muted">
                                        Suche nach: "{{ $search }}"
                                    </small>
                                @endif
                            </div>

                            <!-- Sortierung -->
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted">Sortieren nach:</span>
<select
    wire:model.change="sortBy"
    class="form-select form-select-sm"
    style="width: auto;"
>
    <option value="title">Name</option>
    <option value="flight_hours">Flugdauer</option>
    <option value="price_flight">Preis</option>
</select>

                                <button
                                    wire:click="$toggle('sortDirection')"
                                    class="btn btn-outline-secondary btn-sm"
                                    title="Sortierrichtung ändern"
                                >
                                    <i class="fas fa-sort-amount-{{ $sortDirection === 'asc' ? 'down' : 'up' }}"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Filters -->
@if($this->hasActiveFilters())
    <div class="card shadow-sm mb-4">
        <div class="card-body py-2">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <small class="text-muted me-2">Aktive Filter:</small>

                @foreach($this->getActiveFilters() as $filter => $value)
                    <span class="badge bg-warning text-dark d-flex align-items-center">
                        {{ $value }}
                        <button
                            wire:click="removeFilter('{{ $filter }}')"
                            class="btn-close btn-close-dark ms-2"
                            style="font-size: .6rem"
                        ></button>
                    </span>
                @endforeach
            </div>
        </div>
    </div>
@endif

                <!-- Resultate -->
                <div class="row">
                    @forelse($results as $location)
                        <div class="col-md-6 col-xl-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                @if($location->image1_path)
<img
    src="{{ Storage::url($location->image1_path) }}"
    class="card-img-top object-fit-cover"
    alt="{{ $location->title }}"
    style="height: 180px; filter: brightness(0.9);"
    loading="lazy"
>
                                @endif
 <div class="card-body d-flex flex-column">

    {{-- Titel --}}
    <h5 class="card-title mb-1">
        <a href="{{ route('location.show', $location->slug) }}"
           class="text-decoration-none text-dark">
            {{ $location->title }}
        </a>
    </h5>

    {{-- Land --}}
    <div class="text-muted small mb-2">
        <i class="fas fa-map-marker-alt me-1"></i>
        {{ $location->country->title ?? 'Unbekannt' }}
    </div>

    {{-- Meta Infos --}}
    <ul class="list-unstyled small text-muted mb-3">
        @if($location->flight_hours)
            <li>
                <i class="fas fa-plane me-1"></i>
                {{ number_format($location->flight_hours, 1) }}h Flugzeit
            </li>
        @endif

        @if($location->price_flight)
            <li>
                <i class="fas fa-euro-sign me-1"></i>
                ab {{ number_format($location->price_flight, 0, ',', '.') }} €
            </li>
        @endif
    </ul>

    {{-- Spacer --}}
    <div class="mt-auto"></div>

    {{-- CTA --}}
    <a
        href="{{ route('location.show', $location->slug) }}"
        class="btn btn-warning btn-sm w-100 fw-semibold"
    >
        <i class="fas fa-info-circle me-1"></i> Details ansehen
    </a>

</div>




                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-search fa-4x text-muted mb-3"></i>
                                <h4>Keine Reiseziele gefunden</h4>
                                <p class="text-muted mb-4">
                                    Versuchen Sie andere Filter oder suchen Sie nach einem anderen Begriff.
                                </p>
                                <button
                                    wire:click="resetAllFilters"
                                    class="btn btn-primary"
                                >
                                    Alle Filter zurücksetzen
                                </button>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($results->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $results->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div wire:loading class="loading-overlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Lädt...</span>
        </div>
    </div>
</div>

@push('styles')
<style>
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .filter-section::-webkit-scrollbar {
        width: 6px;
    }

    .filter-section::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .filter-section::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .filter-section::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }

.card {
    transition: box-shadow .2s ease;
}

.card:hover {
    box-shadow: 0 8px 20px rgba(0,0,0,.12);
}

    .continent-row {
        cursor: pointer;
    }

    .continent-row:hover {
        background: #f8f9fa;
    }

    .card-text {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

.form-label {
    color: #495057;
}

.form-select-sm {
    padding-top: .35rem;
    padding-bottom: .35rem;
}

.card-header {
    background-color: #fff;
}

/* Alte Styles killen */
.form-select.bg-primary {
    background-color: #fff !important;
}

.filter-input {
    all: unset;
}

/* Labels sauber */
.form-label {
    margin-bottom: .25rem;
}



</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        // Smooth Scroll bei Pagination
        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            respond(() => {
                succeed(() => {
                    if (component.serverMemo.data.page > 1) {
                        window.scrollTo({
                            top: document.querySelector('.col-lg-9').offsetTop - 20,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    });
</script>
@endpush
