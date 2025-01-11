<div>
    <h3>Bilder bearbeiten</h3>

    @foreach ([1, 2, 3] as $index)
        <div class="mb-4">
            <label for="image{{ $index }}" class="form-label">Bild {{ $index }}</label>
            <div class="d-flex align-items-center gap-2">
                <input type="text" wire:model="query{{ $index }}" placeholder="Suche nach Bild..." class="form-control">
                <button wire:click="searchImages({{ $index }})" class="btn btn-primary">Suchen</button>
            </div>
            <div class="mt-2">
                @if (${"searchResults{$index}"})
                    <div class="row">
                        @foreach (${"searchResults{$index}"} as $result)
                            <div class="col-md-3 mb-2">
                                <img src="{{ $result['preview_url'] }}" class="img-thumbnail">
                                <button wire:click="selectImage({{ $index }}, '{{ $result['full_url'] }}')" class="btn btn-sm btn-success mt-1">Auswählen</button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="mt-3">
                <label for="upload{{ $index }}" class="form-label">Individuelles Bild hochladen</label>
                <input type="file" wire:model="newImage{{ $index }}" class="form-control">
                @error("newImage{$index}") <span class="text-danger">{{ $message }}</span> @enderror
                <button wire:click="uploadImage({{ $index }})" class="btn btn-secondary mt-2">Hochladen</button>
            </div>
            @if (${"textPic{$index}"})
            <div class="mt-3">
                <h5>Aktuelles Bild:</h5>
                <img src="{{ ${"textPic{$index}"} }}" class="img-thumbnail">
            </div>
        @endif

        </div>
    @endforeach
</div>
