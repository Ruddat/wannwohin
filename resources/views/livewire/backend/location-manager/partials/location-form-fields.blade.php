
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row row-cards">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Kontinent</label>
            <select wire:model.change="continentId" class="form-control">
                <option value="">-- Wähle einen Kontinent --</option>
                @foreach($continents as $continent)
                    <option value="{{ $continent->id }}">{{ $continent->title }}</option>
                @endforeach
            </select>
            @error('continentId') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Land</label>
            <select wire:model="countryId" class="form-control">
                <option value="">-- Wähle ein Land --</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->title }}</option>
                @endforeach
            </select>
            @error('countryId') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Titel</label>
            <input wire:model="title" type="text" class="form-control">
            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Alias</label>
            <input wire:model="alias" type="text" class="form-control">
            @error('alias') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Bundesstaat (Lang)</label>
            <input wire:model="bundesstaatLong" type="text" class="form-control">
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Bundesstaat (Kurz)</label>
            <input wire:model="bundesstaatShort" type="text" class="form-control">
        </div>
    </div>

    <div class="col-md-2">
        <div class="mb-3">
            <label class="form-label">ISO2</label>
            <input wire:model="iso2" type="text" class="form-control">
        </div>
    </div>

    <div class="col-md-2">
        <div class="mb-3">
            <label class="form-label">ISO3</label>
            <input wire:model="iso3" type="text" class="form-control">
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Breitengrad (lat)</label>
            <input wire:model="lat" type="text" class="form-control">
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Längengrad (lon)</label>
            <input wire:model="lon" type="text" class="form-control">
        </div>
    </div>

    <div class="col-md-12">
        <div class="mb-3">
            <button type="button" wire:click="fetchGeocodeData" class="btn btn-secondary">
                <i class="ti ti-map-pin"></i> Daten automatisch füllen
            </button>
            <div wire:loading wire:target="fetchGeocodeData" class="text-muted">Daten werden abgerufen...</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">IATA Code</label>
            <input wire:model="iataCode" type="text" class="form-control">
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Flugstunden</label>
            <input wire:model="flightHours" type="number" step="0.01" class="form-control">
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Stopover</label>
            <input wire:model="stopOver" type="number" class="form-control">
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Entfernung von FRA</label>
            <input wire:model="distFromFRA" type="number" class="form-control">
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Entfernungstyp</label>
            <input wire:model="distType" type="text" class="form-control">
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Station ID</label>
            <input wire:model="stationId" type="text" class="form-control">
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Keine Stadt, aber</label>
            <input wire:model="noCityBut" type="text" class="form-control">
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Bevölkerung</label>
            <input wire:model="population" type="number" class="form-control">
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select wire:model="status" class="form-control">
                <option value="active">Aktiv</option>
                <option value="pending">Ausstehend</option>
                <option value="inactive">Inaktiv</option>
            </select>
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Fertiggestellt</label>
            <select wire:model="finished" class="form-control">
                <option value="1">Ja</option>
                <option value="0">Nein</option>
            </select>
        </div>
    </div>
</div>
