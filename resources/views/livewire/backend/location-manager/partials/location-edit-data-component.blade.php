<div>
    <h4>Daten bearbeiten</h4>
    <form wire:submit.prevent="save">
        <div class="mb-3">
            <label for="iata_code" class="form-label">IATA Code</label>
            <input wire:model="iata_code" type="text" class="form-control" id="iata_code">
            @error('iata_code') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="latitude" class="form-label">Breitengrad</label>
            <input wire:model="latitude" type="text" class="form-control" id="latitude">
            @error('latitude') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="longitude" class="form-label">Längengrad</label>
            <input wire:model="longitude" type="text" class="form-control" id="longitude">
            @error('longitude') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="population" class="form-label">Einwohner</label>
            <input wire:model="population" type="number" class="form-control" id="population">
            @error('population') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="time_zone" class="form-label">Zeitzone</label>
            <input wire:model="time_zone" type="text" class="form-control" id="time_zone">
            @error('time_zone') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="price_flight" class="form-label">Flugpreis</label>
            <input wire:model="price_flight" type="number" class="form-control" id="price_flight" step="0.01">
            @error('price_flight') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="price_hotel" class="form-label">Hotelpreis</label>
            <input wire:model="price_hotel" type="number" class="form-control" id="price_hotel" step="0.01">
            @error('price_hotel') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label for="price_rental" class="form-label">Mietwagenpreis</label>
            <input wire:model="price_rental" type="number" class="form-control" id="price_rental" step="0.01">
            @error('price_rental') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Speichern</button>
    </form>
</div>
