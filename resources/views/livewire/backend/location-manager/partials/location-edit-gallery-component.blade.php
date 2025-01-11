<div>
    <h3>Galerie bearbeiten</h3>

    <!-- Suchfeld für Pixabay-Bilder -->
    <div class="mb-4">
        <label for="query" class="form-label">Bildersuche</label>
        <input type="text" wire:model="query" placeholder="Suchbegriff eingeben" class="form-control">
        @error('query') <span class="text-danger">{{ $message }}</span> @enderror
        <button wire:click="searchImages" class="btn btn-primary mt-2">Suchen</button>
    </div>

    <!-- Suchergebnisse anzeigen -->
    @if ($searchResults)
        <h4>Suchergebnisse</h4>
        <div class="row">
            @foreach ($searchResults as $result)
                <div class="col-md-3 mb-4">
                    <img src="{{ $result['preview_url'] }}" class="img-thumbnail" alt="Suchergebnis">
                    <button wire:click="selectImage('{{ $result['full_url'] }}', '{{ $result['tags'] }}')" class="btn btn-success btn-sm mt-2">
                        Hinzufügen
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Hochladen eines neuen Bildes -->
    <div class="mb-4">
        <label for="newImage" class="form-label">Neues Bild hochladen</label>
        <input type="file" wire:model="newImage" class="form-control">
        @error('newImage') <span class="text-danger">{{ $message }}</span> @enderror
        <button wire:click="uploadImage" class="btn btn-primary mt-2">Hochladen</button>
    </div>

    <!-- Hochladen mehrerer Bilder -->
    <div class="mb-4">
        <label for="newImages" class="form-label">Neue Bilder hochladen</label>
        <input type="file" wire:model="newImages" multiple class="form-control">
        @error('newImages.*') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="mb-4">
        @foreach ($newImages as $index => $image)
            <div class="row align-items-center mb-3">
                <div class="col-md-3">
                    <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="img-thumbnail">
                </div>
                <div class="col-md-6">
                    <input type="text" wire:model.defer="captions.{{ $index }}" class="form-control" placeholder="Bildbeschreibung (optional)">
                </div>
                <div class="col-md-3">
                    <button wire:click="uploadSingleImage({{ $index }})" class="btn btn-success">Hochladen</button>
                </div>
            </div>
        @endforeach
    </div>


    <!-- Galerie anzeigen -->
    <h4>Galerie</h4>
    <div class="row">
        @foreach ($galleryImages as $image)
            <div class="col-md-3 mb-4">
                <img src="{{ Storage::url($image->image_path) }}" class="img-thumbnail" alt="Gallery Image">
                <button wire:click="deleteImage({{ $image->id }})" class="btn btn-danger btn-sm mt-2">Löschen</button>
            </div>
        @endforeach
    </div>
</div>
