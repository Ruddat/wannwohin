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

                        <!-- Extra Meta -->
                        <div class="mb-3">
                            <label class="form-label">Extra Meta</label>
                            <div id="extraMetaFields">
                                @foreach($extraMetaFields as $index => $field)
                                    <div class="row mb-2">
                                        <div class="col">
                                            <input type="text" class="form-control" placeholder="Key" wire:model="extraMetaFields.{{ $index }}.key">
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control" placeholder="Value" wire:model="extraMetaFields.{{ $index }}.value">
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-danger" wire:click="removeExtraMetaField({{ $index }})">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                                <button type="button" class="btn btn-outline-primary" wire:click="addExtraMetaField">
                                    <i class="ti ti-plus"></i> Feld hinzufügen
                                </button>
                            </div>
                            @error('extraMeta') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <!-- Keywords mit Dropdown -->
                        <div class="mb-3">
                            <label class="form-label">Keywords</label>
                            <div id="keywordsFields">
                                @foreach($keywordsFields as $index => $field)
                                    <div class="row mb-2">
                                        <div class="col">
                                            <select class="form-select" wire:model="keywordsFields.{{ $index }}.key">
                                                <option value="">-- Schlüssel wählen oder eingeben --</option>
                                                @foreach($commonKeywordKeys as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                                @if(!in_array($field['key'], array_keys($commonKeywordKeys)))
                                                    <option value="{{ $field['key'] }}" selected>{{ $field['key'] }} (benutzerdefiniert)</option>
                                                @endif
                                            </select>
                                            <input type="text" class="form-control mt-1" placeholder="Benutzerdefinierter Schlüssel" wire:model="keywordsFields.{{ $index }}.key" list="keywordSuggestions">
                                            <datalist id="keywordSuggestions">
                                                @foreach($commonKeywordKeys as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </datalist>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control" placeholder="Value" wire:model="keywordsFields.{{ $index }}.value">
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-danger" wire:click="removeKeywordField({{ $index }})">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                                <button type="button" class="btn btn-outline-primary" wire:click="addKeywordField">
                                    <i class="ti ti-plus"></i> Keyword hinzufügen
                                </button>
                            </div>
                            @error('keywords') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Speichern</button>
                            <a href="{{ route('verwaltung.seo-table-manager.seo.table') }}" class="btn btn-secondary">Zurück zur Tabelle</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
