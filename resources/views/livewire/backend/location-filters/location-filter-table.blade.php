<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Standort-Filter verwalten</h5>
        <div>
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
            <span class="input-group-text"><i class="fa fa-search"></i></span>
            <input type="text"
                   class="form-control"
                   placeholder="Suche nach Standort, Typ oder Überschrift..."
                   wire:model.live.debounce.100ms="search">
        </div>

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
                        <div class="col-md-12">
                            <textarea class="form-control" rows="3" placeholder="Text" wire:model="editText"></textarea>
                            @error('editText') <span class="text-danger">{{ $message }}</span> @enderror
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
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th wire:click="sortBy('id')" style="cursor: pointer;">
                            ID
                            @if($sortField === 'id')
                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('location.title')" style="cursor: pointer;">
                            Standort
                            @if($sortField === 'location.title')
                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </th>
                        <th>Kategorie</th>
                        <th>Überschrift</th>
                        <th>Text</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($filters as $filter)
                        <tr>
                            <td>{{ $filter->id }}</td>
                            <td>{{ $filter->location?->title ?? 'Unbekannt' }}</td>
                            <td>{{ $filter->text_type }}</td>
                            <td>{{ $filter->uschrift }}</td>
                            <td>{{ Str::limit($filter->text, 50) }}</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-primary" wire:click="edit({{ $filter->id }})">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm"
                                            wire:click="delete({{ $filter->id }})"
                                            wire:confirm="Sicher, dass Sie diesen Eintrag löschen möchten?">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fa fa-search-off"></i> Keine Einträge gefunden
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $filters->links() }}
        </div>
    </div>
</div>

@push('scripts')
        <!-- SweetAlert2 einbinden -->
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('show-toast', ({ type, message }) => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: type, // 'success' oder 'error'
                        title: message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                });
            });
        </script>
    @endpush
