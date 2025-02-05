<div>
    <h4 class="fw-bold">Erweiterter Standortfilter</h4>

    <!-- Schritt 1: Kategorien auswählen -->
    <div class="mb-3">
        <label class="fw-bold">Was möchtest du erleben?</label>
        <select wire:model.live="selectedCategories" multiple class="form-control">
            @foreach($categories as $category)
                <option value="{{ $category }}">{{ $category }}</option>
            @endforeach
        </select>
    </div>

    <!-- Schritt 2: Suchfeld mit Autocomplete -->
    @if(!empty($selectedCategories))
        <div class="mb-3" x-data="{ open: false }">
            <label class="fw-bold">Welche Überschrift trifft auf dich zu?</label>
            <input type="text" wire:model="searchQuery"
                   @focus="open = true" @blur="setTimeout(() => open = false, 200)"
                   placeholder="Überschrift suchen..."
                   class="form-control">

            <!-- Autocomplete Vorschläge -->
            @if(!empty($filteredUschriften))
                <ul class="list-group mt-2 position-absolute w-100 bg-white shadow" x-show="open">
                    @foreach($filteredUschriften as $uschrift)
                        <li class="list-group-item list-group-item-action" wire:click="selectUschrift('{{ $uschrift }}')" wire:key="uschrift-{{ $uschrift }}">
                            {{ $uschrift }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <!-- Schritt 3: Gewählte Überschriften anzeigen -->
    @if(!empty($selectedUschriften))
        <div class="mb-3">
            <h5>Gewählte Überschriften:</h5>
            <div class="d-flex flex-wrap gap-2">
                @foreach($selectedUschriften as $uschrift)
                    <span class="badge bg-primary p-2" wire:key="selected-{{ $uschrift }}">
                        {{ $uschrift }}
                        <button wire:click="removeUschrift('{{ $uschrift }}')" class="ms-2">&times;</button>
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Schritt 4: Passende Locations anzeigen -->
    @if(!empty($filteredLocations))
        <div class="mt-4">
            <h5>Passende Locations:</h5>
            <ul class="list-group">
                @foreach($filteredLocations as $location)
                    <li class="list-group-item d-flex justify-content-between align-items-center" wire:key="location-{{ $location->id }}">
                        <strong>{{ $location->title }}</strong>
                        <button wire:click="showLocationDetails({{ $location->id }})" class="btn btn-sm btn-primary">
                            Details anzeigen
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Schritt 5: Details zur gewählten Location anzeigen -->
    @if($selectedLocation)
        <div class="mt-4 p-3 border rounded bg-light">
            <h5>Details für {{ $selectedLocation->title }}</h5>
            <ul>
                @foreach($selectedLocation->filters as $filter)
                    <li><strong>{{ $filter->uschrift }}:</strong> {{ $filter->text }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
