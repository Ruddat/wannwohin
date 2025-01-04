<div class="custom-dropdown">
    <select wire:model.change="continentId" class="form-select">
        <option value="" selected>Wähle einen Kontinent</option>
        @foreach ($continents as $continent)
            <option value="{{ $continent->id }}">{{ $continent->title }}</option>
        @endforeach
    </select>
</div>
