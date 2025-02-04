<div>
    <h3 class="mb-3">Texte für Standort verwalten</h3>

    {{-- Schritt 1: Neue Texte hinzufügen --}}
    <div class="card mb-4">
        <div class="card-header">Neuen Text hinzufügen</div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Kategorie</label>
                <input type="text" wire:model="newTextType" class="form-control">
                @error('newTextType') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Überschrift</label>
                <input type="text" wire:model="newUschrift" class="form-control">
                @error('newUschrift') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Text</label>
                <textarea wire:model="newText" class="form-control"></textarea>
                @error('newText') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <button class="btn btn-primary" wire:click="addText">Speichern</button>
        </div>
    </div>

    {{-- Schritt 2: Filter nach Kategorie --}}
    <div class="mb-3">
        <label class="form-label">Kategorien auswählen:</label>
        <select wire:model.live="selectedTypes" multiple class="form-control">
            @foreach($textTypeOptions as $type)
                <option value="{{ $type }}">{{ $type }}</option>
            @endforeach
        </select>
    </div>

    {{-- Schritt 3: Filter nach Überschrift --}}
    @if (!empty($selectedTypes))
        <div class="mb-3">
            <label class="form-label">Überschrift auswählen:</label>
            <select wire:model.live="selectedUschrift" multiple class="form-control">
                @foreach($uschrifts as $uschrift)
                    <option value="{{ $uschrift }}">{{ $uschrift }}</option>
                @endforeach
            </select>
        </div>
    @endif

    {{-- Schritt 4: Ergebnisse anzeigen --}}
    @if (!empty($texts))
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Kategorie</th>
                    <th>Überschrift</th>
                    <th>Text</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($texts as $text)
                    <tr>
                        <td>{{ $text->text_type }}</td>
                        <td>{{ $text->uschrift }}</td>
                        <td>{{ $text->text }}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" wire:click="deleteText({{ $text->id }})">
                                Löschen
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Keine passenden Texte gefunden.</p>
    @endif
</div>
