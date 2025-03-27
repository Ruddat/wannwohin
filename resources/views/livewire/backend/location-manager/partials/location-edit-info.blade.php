<div>
    <h3>Standortinformationen bearbeiten <strong> {{ $title }} </strong></h3>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save">
        <h5>{{ $locationId ? 'Standort bearbeiten' : 'Neue Location erstellen' }}</h5>
        <div class="row row-cards">
            <div class="col-sm-6 col-md-6">

        <div class="mb-3">
            <label for="continentId" class="form-label">Kontinent</label>
            <select wire:model.change="continentId" class="form-control">
                <option value="">-- Wähle einen Kontinent --</option>
                @foreach($continents as $continent)
                    <option value="{{ $continent->id }}">{{ $continent->title }}</option>
                @endforeach
            </select>
            @error('continentId') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

            </div>

            <div class="col-sm-6 col-md-6">
        <div class="mb-3">
            <label for="countryId" class="form-label">Land</label>
            <select wire:model="countryId" class="form-control" wire:ignore.self>
                <option value="">-- Wähle ein Land --</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ $country->id == $countryId ? 'selected' : '' }}>
                        {{ $country->title }}
                    </option>
                @endforeach
            </select>
            @error('countryId') <span class="text-danger">{{ $message }}</span> @enderror
            <div wire:loading wire:target="updatedContinentId" class="text-muted">Länder werden geladen...</div>
        </div>

            </div>
            <div class="col-sm-6 col-md-6">
            <div class="mb-3">
                <label for="title" class="form-label">Titel</label>
                <input wire:model="title" type="text" class="form-control" id="title">
            </div>
            </div>
            <div class="col-sm-6 col-md-6">
            <div class="mb-3">
                <label for="alias" class="form-label">Alias</label>
                <input wire:model="alias" type="text" class="form-control" id="alias">
            </div>
            </div>


<!-- ab hier button nomination  -->
<div class="col-sm-6 col-md-6">
<div class="mb-3">
    <label for="bundesstaatLong" class="form-label">Bundesstaat (Lang)</label>
    <input wire:model="bundesstaatLong" type="text" class="form-control" id="bundesstaatLong">
</div>
</div>

<div class="col-sm-6 col-md-6">
<div class="mb-3">
    <label for="bundesstaatShort" class="form-label">Bundesstaat (Kurz)</label>
    <input wire:model="bundesstaatShort" type="text" class="form-control" id="bundesstaatShort">
</div>
</div>


<!-- ISO2 -->
<div class="col-sm-6 col-md-2">
    <div class="mb-3">
        <label for="iso2" class="form-label">ISO2</label>
        <input wire:model="iso2" type="text" class="form-control" id="iso2">
    </div>
</div>

<!-- ISO3 -->
<div class="col-sm-6 col-md-2">
    <div class="mb-3">
        <label for="iso3" class="form-label">ISO3</label>
        <input wire:model="iso3" type="text" class="form-control" id="iso3">
    </div>
</div>

<!-- Breitengrad -->
<div class="col-sm-6 col-md-4">
    <div class="mb-3">
        <label for="lat" class="form-label">Breitengrad</label>
        <input wire:model="lat" type="text" class="form-control" id="lat">
    </div>
</div>

<!-- Längengrad -->
<div class="col-sm-6 col-md-4">
    <div class="mb-3">
        <label for="lon" class="form-label">Längengrad</label>
        <input wire:model="lon" type="text" class="form-control" id="lon">
    </div>
</div>



<div class="col-sm-6 col-md-12">
    <div class="mb-3">
        <button type="button" wire:click="fetchGeocodeData" class="btn btn-secondary" wire:loading.attr="disabled">
            <i class="ti ti-map-pin"></i> Daten automatisch füllen
        </button>
        <div wire:loading wire:target="fetchGeocodeData" class="text-muted">Daten werden abgerufen...</div>
    </div>
</div>
<!-- ende nomination  -->

<div class="col-sm-6 col-md-3">
        <div class="mb-3">
            <label for="iataCode" class="form-label">IATA Code</label>
            <input wire:model="iataCode" type="text" class="form-control" id="iataCode">
        </div>
</div>

<div class="col-sm-6 col-md-3">

<div class="mb-3">
            <label for="flightHours" class="form-label">Flugstunden</label>
            <input wire:model="flightHours" type="number" class="form-control" id="flightHours" step="0.01">
        </div>
</div>

<div class="col-sm-6 col-md-3">
        <div class="mb-3">
            <label for="stopOver" class="form-label">Stopover</label>
            <input wire:model="stopOver" type="number" class="form-control" id="stopOver">
        </div>
</div>

<div class="col-sm-6 col-md-3">
        <div class="mb-3">
            <label for="distFromFRA" class="form-label">Entfernung von FRA</label>
            <input wire:model="distFromFRA" type="number" class="form-control" id="distFromFRA">
        </div>
</div>


<div class="col-sm-6 col-md-3">
        <div class="mb-3">
            <label for="distType" class="form-label">Entfernungstyp</label>
            <input wire:model="distType" type="text" class="form-control" id="distType">
        </div>
</div>

<div class="col-sm-6 col-md-3">
        <div class="mb-3">
            <label for="stationId" class="form-label">Station ID</label>
            <input wire:model="stationId" type="text" class="form-control" id="stationId">
        </div>
</div>

<div class="col-sm-6 col-md-3">
        <div class="mb-3">
            <label for="noCityBut" class="form-label">Keine Stadt, aber</label>
            <input wire:model="noCityBut" type="text" class="form-control" id="noCityBut">
        </div>
</div>

<div class="col-sm-6 col-md-3">
        <div class="mb-3">
            <label for="population" class="form-label">Bevölkerung</label>
            <input wire:model="population" type="number" class="form-control" id="population">
        </div>
</div>

<hr>
<div class="col-sm-6 col-md-3">
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select wire:model="status" class="form-control">
            <option value="active">Aktiv</option>
            <option value="pending">Ausstehend</option>
            <option value="inactive">Inaktiv</option>
        </select>
        @error('status') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
</div>

<div class="col-sm-6 col-md-3">
    <div class="mb-3">
        <label for="finished" class="form-label">Fertiggestellt</label>
        <select wire:model="finished" class="form-control">
            <option value="1">Ja</option>
            <option value="0">Nein</option>
        </select>
        @error('finished') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
</div>

@if (session()->has('message'))
<div class="alert alert-success">
    {{ session('message') }}
</div>
@endif

@if (session()->has('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif


        <button type="submit" class="btn btn-primary">Speichern</button>

        </div>


    </form>
</div>
