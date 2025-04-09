<div>
    <h3 class="mb-3">Texte für Standort verwalten</h3>

    {{-- Button zum Hinzufügen --}}
    @if(!$isFormVisible)
        <div class="mb-4">
            <button class="btn btn-success" wire:click="showForm">
                <i class="bi bi-plus-lg me-1"></i> Neuen Text hinzufügen
            </button>
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
                            <button class="btn btn-primary me-2" wire:click="updateText">
                                <i class="bi bi-save me-1"></i> Speichern
                            </button>
                            <button class="btn btn-secondary" wire:click="hideForm">
                                <i class="bi bi-x-lg me-1"></i> Abbrechen
                            </button>
                        @else
                            <button class="btn btn-primary" wire:click="addText">
                                <i class="bi bi-plus-lg me-1"></i> Hinzufügen
                            </button>
                            <button class="btn btn-secondary" wire:click="hideForm">
                                <i class="bi bi-x-lg me-1"></i> Abbrechen
                            </button>
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
                    <i class="fa-solid fa-times fa-fw"></i>
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
                    <i class="fa-solid fa-times fa-fw"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Ergebnisse mit Pagination --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="d-none d-md-table-cell">Kategorie</th>
                        <th class="d-none d-md-table-cell">Zusätzliche Kategorie</th>
                        <th>Überschrift</th>
                        <th class="d-none d-lg-table-cell">Text</th>
                        <th class="d-none d-lg-table-cell">Zusatzinfo</th>
                        <th>Aktiv</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($texts as $text)
                        <tr>
                            <td class="d-none d-md-table-cell">{{ $text->text_type }}</td>
                            <td class="d-none d-md-table-cell">{{ $text->category ?? 'N/A' }}</td>
                            <td>{{ $text->uschrift }}</td>
                            <td class="d-none d-lg-table-cell">{{ $text->text }}</td>
                            <td class="d-none d-lg-table-cell">{{ $text->addinfo ? Str::limit($text->addinfo, 50) : 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $text->is_active ? 'success' : 'secondary' }}" wire:click="toggleActive({{ $text->id }})" style="cursor: pointer;">
                                    {{ $text->is_active ? 'Ja' : 'Nein' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary btn-sm" wire:click="editFilterText({{ $text->id }})" title="Bearbeiten">
                                        <i class="ti ti-pencil"></i>
                                    </button>
                                    @if($confirming === $text->id)
                                        <button wire:click="kill({{ $text->id }})" class="btn btn-danger btn-sm" title="Sicher?">
                                            <i class="ti ti-trash"></i> Sicher?
                                        </button>
                                    @else
                                        <button wire:click="confirmDelete({{ $text->id }})" class="btn btn-danger btn-sm" title="Löschen">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    @endif
                                </div>
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
    </div>
    {{-- Pagination --}}
    @if($texts->hasPages())
        <div class="mt-3">
            {{ $texts->links('vendor.livewire.custom-pagination') }}
        </div>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                console.log('Livewire initialized');

                Livewire.on('show-toast', ({ type, message }) => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: type,
                        title: message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        background: type === 'success' ? '#d4edda' : '#f8d7da',
                        color: '#000',
                    });
                });
            });
        </script>
    @endpush
</div>
