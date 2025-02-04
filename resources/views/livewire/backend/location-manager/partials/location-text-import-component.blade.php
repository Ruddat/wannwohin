<div>
    <h3>Excel-Import für Standort-Texte</h3>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <input type="file" wire:model="file" class="form-control">
    @error('file') <span class="text-danger">{{ $message }}</span> @enderror

    <button class="btn btn-primary mt-2" wire:click="import">Importieren</button>
</div>
