<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $parkId ? 'Park bearbeiten' : 'Neuen Park erstellen' }}</h3>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" wire:model="name">
                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Land</label>
                    <input type="text" class="form-control" wire:model="country">
                    @error('country') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Latitude</label>
                    <input type="text" class="form-control" wire:model="latitude">
                </div>
                <div class="mb-3">
                    <label class="form-label">Longitude</label>
                    <input type="text" class="form-control" wire:model="longitude">
                </div>
                <div class="mb-3">
                    <label class="form-label">Geöffnet von</label>
                    <input type="datetime-local" class="form-control" wire:model="open_from">
                </div>
                <div class="mb-3">
                    <label class="form-label">Geschlossen von</label>
                    <input type="datetime-local" class="form-control" wire:model="closed_from">
                </div>
                <button type="submit" class="btn btn-primary">Speichern</button>
                <a href="{{ route('park-manager.index') }}" class="btn btn-secondary">Abbrechen</a>
            </form>
        </div>
    </div>
</div>
