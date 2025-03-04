<div class="container-xl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">SEO-Eintrag Bearbeiten</h3>
                </div>
                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->has('preventOverride'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            {{ $errors->first('preventOverride') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($jsonError)
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            {{ $jsonError }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form wire:submit.prevent="update">
                        <div class="mb-3">
                            <label class="form-label" for="modelType">Model-Typ</label>
                            <input type="text" class="form-control" id="modelType" wire:model="modelType" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="modelId">Model-ID</label>
                            <input type="number" class="form-control" id="modelId" wire:model="modelId" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="title">Titel</label>
                            <input type="text" class="form-control" id="title" wire:model="title">
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="description">Beschreibung</label>
                            <textarea class="form-control" id="description" wire:model="description" rows="4"></textarea>
                            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="canonical">Canonical URL</label>
                            <input type="url" class="form-control" id="canonical" wire:model="canonical">
                            @error('canonical') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="image">Bild-URL</label>
                            <input type="url" class="form-control" id="image" wire:model="image">
                            @error('image') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Extra Meta (JSON)</label>
                            <div id="jsoneditor" style="height: 300px; border: 1px solid #ced4da; border-radius: 0.375rem;"></div>
                            @error('extraMeta') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keywords (kommasepariert)</label>
                            <input type="text" class="form-control" wire:model="keywords" placeholder="Urlaub, Wetter, Reiseziele">
                            @error('keywords') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="preventOverride" wire:model="preventOverride">
                                <label class="form-check-label" for="preventOverride">Überschreiben verhindern</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Speichern</button>
                            <a href="{{ route('verwaltung.seo-table-manager.seo.table') }}" class="btn btn-secondary">Zurück zur Tabelle</a> <!-- Konsistenter Link -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<!-- JSON-Editor einbinden (in <head> oder am Ende des Body für besseres Laden) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/9.10.0/jsoneditor.min.css" rel="stylesheet" type="text/css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/9.10.0/jsoneditor.min.js"></script>

<script>
    document.addEventListener('livewire:load', function () {
        const container = document.getElementById('jsoneditor');
        if (!container) {
            console.error('JSONEditor-Container nicht gefunden.');
            return;
        }

        // Initialisiere JSON-Editor
        const options = {
            mode: 'code', // Andere Modi: 'tree', 'view', 'form'
            mainMenuBar: true,
            navigationBar: true,
            statusBar: true,
            onChange: function () {
                const json = editor.get();
                try {
                    const jsonString = JSON.stringify(json, null, 2); // Formatierung für lesbare JSON
                    @this.updateExtraMeta(jsonString);
                } catch (e) {
                    console.error('Ungültiges JSON:', e);
                    @this.jsonError = 'Ungültiges JSON-Format: ' + e.message;
                }
            }
        };

        let editor;
        try {
            editor = new JSONEditor(container, options);
            console.log('JSONEditor initialisiert:', editor);

            // Lade die initialen Daten
            const initialData = @json($extraMeta ?? null);
            editor.set(initialData || {});
            console.log('Initiale Daten geladen:', initialData);
        } catch (e) {
            console.error('Fehler bei der JSONEditor-Initialisierung:', e);
            @this.jsonError = 'Fehler beim Laden des JSON-Editors: ' + e.message;
        }

        // Reaktiviere den Editor, wenn sich extraMeta ändert
        @this.on('extraMeta', (value) => {
            try {
                editor.set(value || {});
                console.log('extraMeta aktualisiert:', value);
            } catch (e) {
                console.error('Fehler beim Setzen von extraMeta:', e);
                @this.jsonError = 'Fehler beim Aktualisieren der JSON-Daten: ' + e.message;
            }
        });
    });
</script>
</div>
