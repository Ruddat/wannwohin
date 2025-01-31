<div class="container mt-4">
    <h3>Weather Stations</h3>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-3">
        <input wire:model="search" type="text" class="form-control" placeholder="Search stations...">
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Station ID</th>
                <th>Name</th>
                <th>Country</th>
                <th>Region</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stations as $station)
                <tr>
                    <td>{{ $station->id }}</td>
                    <td>{{ $station->station_id }}</td>
                    <td>{{ $station->name }}</td>
                    <td>{{ $station->country }}</td>
                    <td>{{ $station->region }}</td>
                    <td>
                        <button wire:click="editStation({{ $station->id }})" class="btn btn-sm btn-warning">Edit</button>
                        <button wire:click="deleteStation({{ $station->id }})" class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $stations->links() }}

    <hr>


    <!-- Add/Edit Modal -->
    @if ($showForm)
    <div class="modal show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editMode ? 'Edit Weather Station' : 'Add Weather Station' }}</h5>
                    <button type="button" wire:click="resetFields" class="btn-close"></button>
                </div>
                <form wire:submit.prevent="{{ $editMode ? 'updateStation' : 'addStation' }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Station ID</label>
                            <input type="text" wire:model="stationId" class="form-control">
                            @error('stationId') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" wire:model="name" class="form-control">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label>Country</label>
                            <input type="text" wire:model="country" class="form-control">
                            @error('country') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label>Region</label>
                            <input type="text" wire:model="region" class="form-control">
                            @error('region') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label>Latitude</label>
                            <input type="number" step="0.0001" wire:model="latitude" class="form-control">
                            @error('latitude') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label>Longitude</label>
                            <input type="number" step="0.0001" wire:model="longitude" class="form-control">
                            @error('longitude') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label>Elevation</label>
                            <input type="number" step="0.01" wire:model="elevation" class="form-control">
                            @error('elevation') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label>Timezone</label>
                            <input type="text" wire:model="timezone" class="form-control">
                            @error('timezone') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label>Inventory (JSON)</label>
                            <textarea wire:model="inventory" class="form-control"></textarea>
                            @error('inventory') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">{{ $editMode ? 'Update' : 'Add' }}</button>
                        <button type="button" wire:click="resetFields" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
