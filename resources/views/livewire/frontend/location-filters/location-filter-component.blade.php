<div>
    <h2 class="mb-4">Reiseziele filtern</h2>

    <div class="row mb-3">
        <div class="col-md-3">
            <label class="form-label">Kontinent</label>
            <select wire:model="continent" class="form-control">
                <option value="">Alle</option>
                @foreach($continents as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Land</label>
            <select wire:model="country" class="form-control">
                <option value="">Alle</option>
                @foreach($countries as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Sportaktivitäten</label>
            <input type="checkbox" wire:model="sports" class="form-check-input">
        </div>

        <div class="col-md-3">
            <label class="form-label">Freizeitparks</label>
            <input type="checkbox" wire:model="freizeitparks" class="form-check-input">
        </div>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Reiseziel</th>
                <th>Land</th>
                <th>Kontinent</th>
                <th>Sport</th>
                <th>Freizeitpark</th>
            </tr>
        </thead>
        <tbody>
            @foreach($locations as $location)
                <tr>
                    <td>{{ $location->location }}</td>
                    <td>{{ $location->country }}</td>
                    <td>{{ $location->continent }}</td>
                    <td>{{ $location->list_sports ? '✅' : '❌' }}</td>
                    <td>{{ $location->list_amusement_park ? '✅' : '❌' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
