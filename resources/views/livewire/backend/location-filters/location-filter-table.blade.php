<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Standort-Filter verwalten</h5>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-success" wire:click="startCreating">
                <i class="fas fa-plus"></i> Neuen Filter erstellen
            </button>
            <select wire:model.live="perPage" class="form-select form-select-sm">
                <option value="10">10 pro Seite</option>
                <option value="25">25 pro Seite</option>
                <option value="50">50 pro Seite</option>
                <option value="100">100 pro Seite</option>
            </select>
        </div>
    </div>

    <div class="card-body">
        <!-- Suchfeld -->
        <div class="mb-3 input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text"
                   class="form-control"
                   placeholder="Suche nach Standort, Typ, Kategorie oder Überschrift..."
                   wire:model.live.debounce.300ms="search">
        </div>

        <!-- Erstellungsformular -->
        @if($creating)
            <div class="card mb-3 border-success">
                <div class="card-body">
                    <h5 class="card-title">Neuen Filter erstellen</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="Text-Typ" wire:model="newTextType">
                            @error('newTextType') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="Kategorie" wire:model="newCategory">
                            @error('newCategory') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="Überschrift" wire:model="newUschrift">
                            @error('newUschrift') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model="newLocationId">
                                <option value="">Standort auswählen...</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->title }}</option>
                                @endforeach
                            </select>
                            @error('newLocationId') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <textarea class="form-control" rows="3" placeholder="Text" wire:model="newText"></textarea>
                            @error('newText') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <textarea class="form-control" rows="3" placeholder="Zusatzinfo" wire:model="newAddinfo"></textarea>
                            @error('newAddinfo') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model="newIsActive">
                                <option value="1">Aktiv</option>
                                <option value="0">Inaktiv</option>
                            </select>
                            @error('newIsActive') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-success" wire:click="create">Erstellen</button>
                            <button class="btn btn-secondary" wire:click="resetCreateForm">Abbrechen</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Bearbeitungsformular -->
        @if($editingFilterId)
            <div class="card mb-3 border-primary">
                <div class="card-body">
                    <h5 class="card-title">Filter bearbeiten (ID: {{ $editingFilterId }})</h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="Text-Typ" wire:model="editTextType">
                            @error('editTextType') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="Kategorie" wire:model="editCategory">
                            @error('editCategory') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" placeholder="Überschrift" wire:model="editUschrift">
                            @error('editUschrift') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model="editLocationId">
                                <option value="">Standort auswählen...</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->title }}</option>
                                @endforeach
                            </select>
                            @error('editLocationId') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <textarea class="form-control" rows="3" placeholder="Text" wire:model="editText"></textarea>
                            @error('editText') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6">
                            <textarea class="form-control" rows="3" placeholder="Zusatzinfo" wire:model="editAddinfo"></textarea>
                            @error('editAddinfo') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model="editIsActive">
                                <option value="1">Aktiv</option>
                                <option value="0">Inaktiv</option>
                            </select>
                            @error('editIsActive') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-success" wire:click="update">Speichern</button>
                            <button class="btn btn-secondary" wire:click="cancelEdit">Abbrechen</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabelle -->
        @if($showTable)
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th wire:click="sortBy('id')" style="cursor: pointer;">
                                ID
                                @if($sortField === 'id')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('location.title')" style="cursor: pointer;">
                                Standort
                                @if($sortField === 'location.title')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('text_type')" style="cursor: pointer;">
                                Typ
                                @if($sortField === 'text_type')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('category')" style="cursor: pointer;">
                                Kategorie
                                @if($sortField === 'category')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('uschrift')" style="cursor: pointer;">
                                Überschrift
                                @if($sortField === 'uschrift')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Text</th>
                            <th>Zusatzinfo</th>
                            <th wire:click="sortBy('is_active')" style="cursor: pointer;">
                                Aktiv
                                @if($sortField === 'is_active')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                                Erstellt
                                @if($sortField === 'created_at')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($filters as $filter)
                            <tr>
                                <td>{{ $filter->id }}</td>
                                <td>{{ $filter->location?->title ?? 'Unbekannt' }}</td>
                                <td>{{ $filter->text_type }}</td>
                                <td>{{ $filter->category ?? '-' }}</td>
                                <td>{{ $filter->uschrift }}</td>
                                <td>{{ Str::limit($filter->text, 50) }}</td>
                                <td>{{ $filter->addinfo ? Str::limit($filter->addinfo, 50) : '-' }}</td>
                                <td>
                                    <span class="badge {{ $filter->is_active ? 'bg-success' : 'bg-danger' }}"
                                          wire:click="toggleActive({{ $filter->id }})"
                                          style="cursor: pointer;">
                                        {{ $filter->is_active ? 'Ja' : 'Nein' }}
                                    </span>
                                </td>
                                <td>{{ $filter->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-primary" wire:click="edit({{ $filter->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger"
                                                wire:click="delete({{ $filter->id }})"
                                                wire:confirm="Sicher, dass Sie diesen Eintrag löschen möchten?">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-search-off"></i> Keine Einträge gefunden
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Footer außerhalb des @if-Blocks --}}
        <div class="card-footer">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <select wire:model.change="perPage" class="form-select form-select-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <div class="app-pagination-link">
                        <ul class="pagination app-pagination justify-content-center mb-0">
                            <li class="page-item {{ $filters->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link b-r-left" wire:click="previousPage" href="#" aria-label="Previous">
                                    Previous
                                </a>
                            </li>
                            @php
                                $currentPage = $filters->currentPage();
                                $lastPage = $filters->lastPage();
                                $range = 2;
                                $start = max(1, $currentPage - $range);
                                $end = min($lastPage, $currentPage + $range);
                            @endphp
                            @for ($i = $start; $i <= $end; $i++)
                                <li class="page-item {{ $currentPage == $i ? 'active' : '' }}" aria-current="{{ $currentPage == $i ? 'page' : '' }}">
                                    <a class="page-link" wire:click="gotoPage({{ $i }})" href="#">{{ $i }}</a>
                                </li>
                            @endfor
                            <li class="page-item {{ $filters->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link b-r-right" wire:click="nextPage" href="#" aria-label="Next">
                                    Next
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <span class="text-muted">
                        Zeigt {{ $filters->firstItem() }} bis {{ $filters->lastItem() }} von {{ $filters->total() }} Einträgen
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
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
