<div class="container-xl mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">SEO-Einträge</h3>
                    <div class="input-group w-auto">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" wire:model.change.debounce.100ms="search" class="form-control" placeholder="Suche..." style="max-width: 300px;">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="w-1 cursor-pointer" wire:click="sortBy('id')">
                                        ID
                                        @if($sortField === 'id')
                                            <span class="text-muted">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </th>
                                    <th class="w-1 cursor-pointer" wire:click="sortBy('model_type')">
                                        Model-Typ
                                        @if($sortField === 'model_type')
                                            <span class="text-muted">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </th>
                                    <th class="w-1 cursor-pointer" wire:click="sortBy('model_id')">
                                        Model
                                        @if($sortField === 'model_id')
                                            <span class="text-muted">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </th>
                                    <th class="cursor-pointer" wire:click="sortBy('title')">
                                        Titel
                                        @if($sortField === 'title')
                                            <span class="text-muted">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </th>
                                    <th class="w-1 cursor-pointer" wire:click="sortBy('prevent_override')">
                                        Überschreiben
                                        @if($sortField === 'prevent_override')
                                            <span class="text-muted">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </th>
                                    <th class="w-1 cursor-pointer" wire:click="sortBy('updated_at')">
                                        Zuletzt bearbeitet
                                        @if($sortField === 'updated_at')
                                            <span class="text-muted">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                        @endif
                                    </th>
                                    <th class="w-1">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($seoMetas as $seoMeta)
                                    <tr>
                                        <td>{{ $seoMeta->id }}</td>
                                        <td>{{ $seoMeta->model_type }}</td>
                                        <td>{{ $seoMeta->model_name }}</td>
                                        <td class="text-truncate" style="max-width: 300px;" title="{{ $seoMeta->title }}">{{ $seoMeta->title }}</td>
                                        <td>
                                            <button
                                                wire:click="togglePreventOverride({{ $seoMeta->id }})"
                                                class="btn btn-sm {{ $seoMeta->prevent_override ? 'btn-danger' : 'btn-success' }}"
                                                style="min-width: 80px;"
                                            >
                                                {{ $seoMeta->prevent_override ? 'Gesperrt' : 'Erlaubt' }}
                                            </button>
                                        </td>
                                        <td>{{ $seoMeta->updated_at->format('d.m.Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button wire:click="edit({{ $seoMeta->id }})" class="btn btn-outline-primary btn-sm">
                                                    <i class="ti ti-edit"></i> Bearbeiten
                                                </button>
                                                <button wire:click="confirmDelete({{ $seoMeta->id }})" class="btn btn-outline-danger btn-sm">
                                                    <i class="ti ti-trash"></i> Löschen
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Keine Einträge gefunden.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div>{{ $seoMetas->links() }}</div>
                    <div class="d-flex align-items-center">
                        <label for="perPageSelect" class="me-2 mb-0">Einträge pro Seite:</label>
                        <select wire:model.change="perPage" id="perPageSelect" class="form-select form-select-sm">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bestätigungsdialog für Löschen -->
    @if($deleteId)
        <div class="modal modal-blur fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">SEO-Eintrag löschen</h5>
                        <button type="button" class="btn-close" wire:click="$set('deleteId', null)" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Sind Sie sicher, dass Sie diesen SEO-Eintrag löschen möchten?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('deleteId', null)">Abbrechen</button>
                        <button type="button" class="btn btn-danger" wire:click="delete">Löschen</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('message'))
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-success text-white">
                    <strong class="me-auto">Erfolg</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ session('message') }}
                </div>
            </div>
        </div>
    @endif
</div>
