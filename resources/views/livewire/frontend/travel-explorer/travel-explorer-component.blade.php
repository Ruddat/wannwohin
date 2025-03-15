<div class="travel-explorer">
    <!-- Filter -->
    <div class="filters mb-4">
        <div class="row g-3">
            <div class="col-md-4 col-sm-6">
                <select wire:model.change="selectedMonth" class="form-select">
                    <option value="">@autotranslate('Wann?', app()->getLocale())</option>
                    @foreach ($months as $monthId => $monthName)
                        <option value="{{ $monthId }}">{{ $monthName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 col-sm-6">
                <select wire:model.change="selectedActivities" multiple class="form-select">
                    <option value="">@autotranslate('Was?', app()->getLocale())</option>
                    @foreach ($activityMap as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 col-sm-12">
                <select wire:model.change="selectedContinent" class="form-select">
                    <option value="">@autotranslate('Wo?', app()->getLocale())</option>
                    @foreach ($continents as $id => $title)
                        <option value="{{ $id }}">{{ $title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button wire:click="resetFilters" class="btn btn-secondary mt-3">@autotranslate('Filter zurücksetzen', app()->getLocale())</button>
    </div>

    <!-- Kacheln -->
    <h2 class="suggestions-title">🌍 @autotranslate('Entdecke dein nächstes Abenteuer!', app()->getLocale())</h2>
    <div class="suggestions-container">
        @if ($loading)
            <p class="text-center text-muted">@autotranslate('Lade Reiseziele...', app()->getLocale())</p>
        @elseif (empty($categories) && empty($weather))
            <p class="text-center text-muted">@autotranslate('Keine Kategorien gefunden.', app()->getLocale())</p>
        @else
            <!-- Kategorien und Wetter -->
            <div class="category-grid">
                @foreach (array_merge(['wetter'], array_keys($categories)) as $key)
                    @if ($key === 'wetter')
                        <div class="suggestion-button bg-{{ $categoryMap[$key]['color'] }} small">
                            <i class="{{ $categoryMap[$key]['icon'] }}"></i>
                            @autotranslate($weather['title'], app()->getLocale())
                            <strong>{{ $weather['temp'] }}</strong>
                        </div>
                    @else
                        @php
                            // Überprüfe, ob der Schlüssel in $categoryMap existiert
                            $categoryData = $categoryMap[$key] ?? [
                                'color' => '#cccccc', // Standardfarbe
                                'icon' => 'fas fa-question', // Standardicon
                                'label' => ucfirst($key), // Standardlabel
                            ];
                        @endphp
                        <a href="/{{ $key }}"
                           class="suggestion-button bg-{{ $categoryData['color'] }} {{ $key === 'inspiration' ? 'big' : 'medium' }}">
                            <i class="{{ $categoryData['icon'] }}"></i>
                            @autotranslate($categoryData['label'], app()->getLocale())
                            <span class="suggestion-count">{{ $suggestions[Str::lower($categoryData['label'])] ?? 0 }}</span>
                        </a>
                    @endif
                @endforeach
            </div>

<!-- Reiseziel-Kacheln -->
@if ($locations->isEmpty())
    <p class="text-center text-muted">@autotranslate('Keine Reiseziele gefunden. Passe deine Filter an!', app()->getLocale())</p>
@else
    @foreach ($locations as $location)
        @php
            // Hole die Texte für diese Location
            $locationText = $locationTexts[$location->id][0] ?? null; // Nimm den ersten Eintrag
        @endphp
        <a href="{{ route('location.details', [
            'continent' => $location->country->continent->alias ?? 'unknown',
            'country' => $location->country->alias ?? 'unknown',
            'location' => $location->alias ?? 'unknown'
        ]) }}"
           class="suggestion-button bg-travel animate__animated animate__fadeInUp"
style="background-image: url('{{ $location->text_pic2 ? asset($location->text_pic2) : asset('img/placeholders/location-placeholder.jpg') }}');"           wire:key="{{ $location->id }}">
            <div class="overlay">
                <i class="fas fa-map-marker-alt"></i>
                @autotranslate($location->title, app()->getLocale())
                @if ($location->best_traveltime_json)
                    <span class="badge bg-success">
                        {{ implode(', ', array_map(fn($m) => substr($months[$m], 0, 3), json_decode($location->best_traveltime_json, true))) }}
                    </span>
                @endif
                @if ($locationText)
                    <div class="location-text">
                        <strong>{{ $locationText['title'] }}</strong>
                        <p>{{ Str::limit($locationText['description'], 100) }}</p>
                    </div>
                @endif
            </div>
        </a>
    @endforeach
    <div class="mt-4">
        {{ $locations->links() }}
    </div>
@endif
        @endif
    </div>



<style scoped>
.travel-explorer {
    padding: 2rem 0;
}

.suggestions-title {
    text-align: center;
    font-size: 2rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 1.5rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.suggestions-container {
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-bottom: 2rem;
}

.suggestion-button {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
    color: white;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    min-height: 150px;
    text-align: center;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.suggestion-button.big { grid-column: span 2; min-height: 180px; }
.suggestion-button.medium, .suggestion-button.small { grid-column: span 1; }

.suggestion-button.bg-travel {
    background-size: cover;
    background-position: center;
}

.suggestion-button.bg-travel .overlay {
    background: rgba(0, 0, 0, 0.6);
}

.suggestion-button.bg-erlebnis { background: #9c27b0; }
.suggestion-button.bg-sport { background: #4caf50; }
.suggestion-button.bg-freizeitpark { background: #e91e63; }
.suggestion-button.bg-inspiration { background: #2196f3; }
.suggestion-button.bg-wetter { background: #fbc02d; }

.suggestion-button .overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border-radius: 10px;
    gap: 0.5rem;
}

.suggestion-button i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.suggestion-button .badge {
    font-size: 0.9rem;
    padding: 0.25rem 0.5rem;
}

.suggestion-button .suggestion-count {
    font-size: 1rem;
    margin-top: 0.5rem;
    background: rgba(255, 255, 255, 0.7);
    color: #333;
    padding: 0.25rem 0.5rem;
    border-radius: 5px;
}

.suggestion-button:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
}

.filters .form-select {
    width: 100%;
    padding: 0.5rem;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .category-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .suggestion-button {
        padding: 1rem;
        font-size: 1rem;
        min-height: 120px;
    }

    .suggestion-button.big { grid-column: span 2; min-height: 140px; }
    .suggestion-button.medium, .suggestion-button.small { grid-column: span 1; }

    .suggestion-button i { font-size: 1.5rem; }
    .suggestion-button .suggestion-count { font-size: 0.9rem; }
}
</style>


<style>
    .location-text {
        margin-top: 0.5rem;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.9);
        text-align: left;
    }

    .location-text strong {
        display: block;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }

    .location-text p {
        margin: 0;
        line-height: 1.4;
    }
    </style>

</div>
