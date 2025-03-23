<div>
    <form wire:submit.prevent="save">
        <div class="mb-4">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" wire:model="name" placeholder="Parkname">
            @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="mb-4">
            <label class="form-label">Land</label>
            <input type="text" class="form-control" wire:model="country" placeholder="Deutschland">
            @error('country') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="mb-4">
            <label class="form-label">Standort</label>
            <input type="text" class="form-control" wire:model="location" placeholder="Stadt">
        </div>
        <div class="row mb-4">
            <div class="col-md-5">
                <label class="form-label">Latitude</label>
                <input type="text" class="form-control" wire:model="latitude" placeholder="z. B. 48.137154">
            </div>
            <div class="col-md-5">
                <label class="form-label">Longitude</label>
                <input type="text" class="form-control" wire:model="longitude" placeholder="z. B. 11.576124">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-outline-secondary w-100" wire:click="updateCoordinates">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                        <path d="M12 3v18"/>
                        <path d="M3 12h18"/>
                    </svg>
                    Koordinaten
                </button>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">URL</label>
            <div class="input-group">
                <input type="url" class="form-control" wire:model="url" placeholder="https://example.com">
                <button type="button" class="btn btn-outline-secondary" wire:click="scrapeData">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                        <path d="M3.6 9h16.8"/>
                        <path d="M3.6 15h16.8"/>
                        <path d="M11.5 3a17 17 0 0 0 0 18"/>
                        <path d="M12.5 3a17 17 0 0 1 0 18"/>
                        <path d="M12 16v5"/>
                        <path d="M9 19l3 3l3 -3"/>
                    </svg>
                    Scrapen
                </button>
            </div>
            @error('url') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="mb-4">
            <label class="form-label">Video-Embed-Code (optional)</label>
            <textarea class="form-control" wire:model="embedCode" rows="2" placeholder="z. B. <iframe width='560' height='315' src='https://www.youtube.com/embed/...'></iframe>"></textarea>
            @error('embedCode') <span class="text-danger small">{{ $message }}</span> @enderror
            @if($embedCode)
                <small class="text-muted mt-2">Vorschau:</small>
                <div class="mt-2">
                    {!! $embedCode !!}
                </div>
            @elseif($videoUrl)
                <small class="text-muted mt-2">Vorschau (aus Video-URL):</small>
                <div class="mt-2">
                    @if(str_contains($videoUrl, 'youtube.com'))
                        <iframe width="560" height="315" src="{{ str_replace('watch?v=', 'embed/', $videoUrl) }}" frameborder="0" allowfullscreen></iframe>
                    @elseif(str_contains($videoUrl, 'vimeo.com'))
                        <iframe width="560" height="315" src="{{ $videoUrl }}" frameborder="0" allowfullscreen></iframe>
                    @elseif($videoUrl)
                        <video width="560" height="315" controls>
                            <source src="{{ $videoUrl }}" type="video/mp4">
                            Ihr Browser unterstützt das Video-Tag nicht.
                        </video>
                    @endif
                </div>
            @endif
        </div>
        <div class="mb-4">
            <label class="form-label">Video-URL (optional, wird ignoriert, wenn Embed-Code vorhanden ist)</label>
            <input type="url" class="form-control" wire:model="videoUrl" placeholder="z. B. https://youtube.com/watch?v=xyz">
            @error('videoUrl') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="mb-4">
            <label class="form-label">Logo hochladen (optional)</label>
            <input type="file" class="form-control" wire:model="logoFile">
            @error('logoFile') <span class="text-danger small">{{ $message }}</span> @enderror
            @if($logoUrl)
                <small class="text-muted mt-2">Aktuelles Logo:</small>
                <div class="mt-2">
                    <img src="{{ $logoUrl }}" alt="Park Logo" class="img-fluid" style="max-width: 200px; max-height: 100px;">
                </div>
            @endif
        </div>
        <div class="mb-4">
            <label class="form-label">Beschreibung</label>
            <textarea class="form-control" wire:model="description" rows="3" placeholder="Kurze Beschreibung des Parks"></textarea>
            @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="mb-4">
            <label class="form-label">Öffnungszeiten</label>
            <div class="mb-3">
                <div class="row align-items-center">
                    <div class="col-4">
                        <input type="time" class="form-control" wire:model="defaultOpen" placeholder="Öffnet">
                        @error('defaultOpen') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-4">
                        <input type="time" class="form-control" wire:model="defaultClose" placeholder="Schließt">
                        @error('defaultClose') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" wire:model="applyToAll" id="applyToAll">
                            <label class="form-check-label" for="applyToAll">Für alle Tage übernehmen</label>
                        </div>
                    </div>
                </div>
            </div>
            @foreach (['monday' => 'Montag', 'tuesday' => 'Dienstag', 'wednesday' => 'Mittwoch', 'thursday' => 'Donnerstag', 'friday' => 'Freitag', 'saturday' => 'Samstag', 'sunday' => 'Sonntag'] as $day => $label)
                <div class="row mb-3 align-items-center">
                    <div class="col-3 col-md-2">
                        <label class="form-label mb-0">{{ $label }}</label>
                    </div>
                    <div class="col-4 col-md-5">
                        <input type="time" class="form-control" wire:model="opening_hours.{{ $day }}.open" placeholder="Öffnet" {{ $applyToAll ? 'disabled' : '' }}>
                        @error("opening_hours.{$day}.open") <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-4 col-md-5">
                        <input type="time" class="form-control" wire:model="opening_hours.{{ $day }}.close" placeholder="Schließt" {{ $applyToAll ? 'disabled' : '' }}>
                        @error("opening_hours.{$day}.close") <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-end">
            <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Abbrechen</button>
            <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2"/>
                    <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                    <path d="M14 4l0 4l-6 0l0 -4"/>
                </svg>
                Speichern
            </button>
        </div>
    </form>
</div>
