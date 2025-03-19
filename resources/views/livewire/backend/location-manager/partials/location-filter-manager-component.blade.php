<div>
    <h3 class="mb-3">Texte für Standort verwalten</h3>

    {{-- Button zum Hinzufügen --}}
    @if(!$isFormVisible)
        <div class="mb-4">
            <button class="btn btn-success" wire:click="showForm">Neuen Text hinzufügen</button>
        </div>
    @endif

    {{-- Formular (für Hinzufügen und Bearbeiten) --}}
    @if($isFormVisible)
        <div class="card mb-4">
            <div class="card-header">{{ $editingTextId ? 'Text bearbeiten' : 'Neuen Text hinzufügen' }}</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Kategorie (Text Type)</label>
                        <input type="text" wire:model="formTextType" class="form-control">
                        @error('formTextType') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Zusätzliche Kategorie</label>
                        <input type="text" wire:model="formCategory" class="form-control">
                        @error('formCategory') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Überschrift</label>
                        <input type="text" wire:model="formUschrift" class="form-control">
                        @error('formUschrift') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Text</label>
                        <textarea wire:model="formText" class="form-control" rows="3"></textarea>
                        @error('formText') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Zusatzinfo</label>
                        <textarea wire:model="formAddinfo" class="form-control" rows="3"></textarea>
                        @error('formAddinfo') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Aktiv</label>
                        <div class="form-check">
                            <input type="checkbox" wire:model="formActiveStatus" class="form-check-input" id="formActiveStatus">
                            <label class="form-check-label" for="formActiveStatus">Text ist aktiv</label>
                        </div>
                    </div>
                    <div class="col-12">
                        @if($editingTextId)
                            <button class="btn btn-primary me-2" wire:click="updateText">Speichern</button>
                            <button class="btn btn-secondary" wire:click="hideForm">Abbrechen</button>
                        @else
                            <button class="btn btn-primary" wire:click="addText">Hinzufügen</button>
                            <button class="btn btn-secondary" wire:click="hideForm">Abbrechen</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Filter (immer sichtbar) --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Kategorien auswählen:</label>
            <div class="input-group">
                <select wire:model.live="selectedTypes" multiple class="form-select" size="5">
                    @foreach($textTypeOptions as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
                <button class="btn btn-outline-secondary" wire:click="resetFilters" title="Filter zurücksetzen">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Überschrift auswählen:</label>
            <div class="input-group">
                <select wire:model.live="selectedUschrift" multiple class="form-select" size="5">
                    @foreach($uschrifts as $uschrift)
                        <option value="{{ $uschrift }}">{{ $uschrift }}</option>
                    @endforeach
                </select>
                <button class="btn btn-outline-secondary" wire:click="resetFilters" title="Filter zurücksetzen">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Ergebnisse mit Pagination --}}
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Kategorie</th>
                    <th>Zusätzliche Kategorie</th>
                    <th>Überschrift</th>
                    <th>Text</th>
                    <th>Zusatzinfo</th>
                    <th>Aktiv</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($texts as $text)
                    <tr>
                        <td>{{ $text->text_type }}</td>
                        <td>{{ $text->category ?? 'N/A' }}</td>
                        <td>{{ $text->uschrift }}</td>
                        <td>{{ $text->text }}</td>
                        <td>{{ $text->addinfo ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-{{ $text->is_active ? 'success' : 'secondary' }}" wire:click="toggleActive({{ $text->id }})" style="cursor: pointer;">
                                {{ $text->is_active ? 'Ja' : 'Nein' }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm me-2" wire:click="editFilterText({{ $text->id }})">
                                Bearbeiten
                            </button>
                            <button class="btn btn-danger btn-sm" wire:click="deleteText({{ $text->id }})">
                                Löschen
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Keine passenden Texte gefunden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($texts->hasPages())
        <div class="mt-3">
            {{ $texts->links() }}
        </div>
    @endif

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
        <script>
            document.addEventListener('livewire:initialized', () => {
                console.log('Livewire initialized');
            });

            Livewire.on('show-toast', ({ type, message }) => {
                Swal.fire({
                    icon: type === 'success' ? 'success' : 'info',
                    title: type === 'success' ? 'Erfolg' : 'Status',
                    text: message,
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        </script>
    @endpush
</div>
