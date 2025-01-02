<div class="card mb-4">
    <div class="card-body">
        <h3>Werbeblock erstellen</h3>
        <form wire:submit.prevent="save">
            <div class="mb-3">
                <label for="title" class="form-label">Titel</label>
                <input type="text" id="title" class="form-control" wire:model="title">
                @error('title') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Inhalt</label>
                <textarea id="content" class="form-control" wire:model="content"></textarea>
                @error('content') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="mb-3">
                <label for="link" class="form-label">Link</label>
                <input type="url" id="link" class="form-control" wire:model="link">
                @error('link') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="mb-3">
                <label for="providerId" class="form-label">Anbieter</label>
                <select id="providerId" class="form-select" wire:model="providerId">
                    <option value="">Wähle einen Anbieter</option>
                    @foreach (\App\Models\ModProviders::all() as $provider)
                        <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                    @endforeach
                </select>
                @error('providerId') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="btn btn-success">Speichern</button>
        </form>
    </div>
</div>
