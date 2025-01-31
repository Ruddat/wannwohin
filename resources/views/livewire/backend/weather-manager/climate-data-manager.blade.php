<div>
    <h2 class="mb-3">Klimadaten Verwaltung</h2>

    <!-- Filter -->
    <div class="d-flex gap-2 mb-3">
        <input type="number" class="form-control" placeholder="Jahr" wire:model.live="year">
        <select class="form-control" wire:model.change="location_id">
            <option value="">-- Wähle eine Location --</option>
            @foreach ($locations as $location)
                <option value="{{ $location->id }}">{{ $location->title }}</option>
            @endforeach
        </select>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <!-- Tabelle -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Location</th>
                <th>Monat</th>
                <th>Jahr</th>
                <th>Ø Temperatur</th>
                <th>Max. Temperatur</th>
                <th>Min. Temperatur</th>
                <th>Niederschlag</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($climateData as $data)
                <tr>
                    <td>{{ $data->id }}</td>
                    <td>{{ $data->location->title ?? 'Unbekannt' }}</td>
                    <td>{{ $data->month_name }}</td>
                    <td>{{ $data->year }}</td>

                    @if (isset($editing['id']) && $editing['id'] == $data->id)

                        <td><input type="text" wire:model="editing.temperature_avg" class="form-control"></td>
                        <td><input type="text" wire:model="editing.temperature_max" class="form-control"></td>
                        <td><input type="text" wire:model="editing.temperature_min" class="form-control"></td>
                        <td><input type="text" wire:model="editing.precipitation" class="form-control"></td>
                        <td>
                            <button class="btn btn-success btn-sm" wire:click="save">Speichern</button>
                        </td>
                    @else
                        <td>{{ $data->temperature_avg }}°C</td>
                        <td>{{ $data->temperature_max }}°C</td>
                        <td>{{ $data->temperature_min }}°C</td>
                        <td>{{ $data->precipitation }} mm</td>
                        <td>
                            <button class="btn btn-primary btn-sm" wire:click="edit({{ $data->id }})">Bearbeiten</button>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $climateData->links() }}

    <!-- Neuer Eintrag -->
    <div class="mt-4">
        <h4>Neuen Eintrag hinzufügen</h4>
        <div class="d-flex gap-2">
            <input type="number" class="form-control" placeholder="Location ID" wire:model="newEntry.location_id">
            <input type="number" class="form-control" placeholder="Monat (1-12)" wire:model="newEntry.month">
            <input type="number" class="form-control" placeholder="Jahr" wire:model="newEntry.year">
            <input type="text" class="form-control" placeholder="Ø Temperatur" wire:model="newEntry.temperature_avg">
            <input type="text" class="form-control" placeholder="Max. Temperatur" wire:model="newEntry.temperature_max">
            <input type="text" class="form-control" placeholder="Min. Temperatur" wire:model="newEntry.temperature_min">
            <input type="text" class="form-control" placeholder="Niederschlag" wire:model="newEntry.precipitation">
            <button class="btn btn-success" wire:click="addNew">Hinzufügen</button>
        </div>
    </div>
</div>
