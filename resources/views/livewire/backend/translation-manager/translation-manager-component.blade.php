<div>
    <div class="card">
        <!-- Page body -->
        <div class="page-body">
            <div class="container-xl">
                <div class="row row-cards">
                    <!-- Card -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Übersetzungen verwalten</h3>
                            </div>

                            <div class="card-body border-bottom py-3">
                                <div class="d-flex flex-column flex-sm-row">
                                    <div class="text-secondary mb-3 mb-sm-0">
                                        <div class="d-inline-block">
                                            <select wire:model.change="perPage" class="form-select form-select-sm w-auto">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="ms-sm-auto text-secondary d-flex">
                                        <div class="me-3">
                                            <select wire:model.change="selectedLocale" class="form-select form-select-sm w-auto">
                                                <option value="">Alle Sprachen</option>
                                                @foreach($locales as $locale)
                                                    <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <!-- Search Icon -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="10" cy="10" r="7" />
                                                    <line x1="21" y1="21" x2="15" y2="15" />
                                                </svg>
                                            </span>
                                            <input wire:model.live="search" type="text" class="form-control form-control-sm" placeholder="Suchen...">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Schlüssel</th>
                                                <th>Sprache</th>
                                                <th>Text</th>
                                                <th class="text-end">Aktionen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($translations as $translation)
                                                <tr>
                                                    <td>{{ $translation->key }}</td>
                                                    <td>{{ $translation->locale }}</td>
                                                    <td>{{ Str::limit($translation->text, 50) }}</td>
                                                    <td class="text-end">
                                                        <button class="btn btn-sm btn-primary" wire:click="edit({{ $translation->id }})">
                                                            Bearbeiten
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" wire:click="delete({{ $translation->id }})">
                                                            Löschen
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">Keine Übersetzungen gefunden.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                {{ $translations->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        @if($editingTranslation)
        <div class="modal modal-blur fade show d-block" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Übersetzung bearbeiten</h5>
                        <button type="button" class="btn-close" wire:click="cancelEdit"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="updateTranslation">
                            <div class="mb-3">
                                <label for="editKey" class="form-label">Schlüssel</label>
                                <input type="text" id="editKey" wire:model="editKey" class="form-control" disabled>
                                @error('editKey') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="editLocale" class="form-label">Sprache</label>
                                <input type="text" id="editLocale" wire:model="editLocale" class="form-control" disabled>
                                @error('editLocale') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="editText" class="form-label">Text</label>
                                <textarea id="editText" wire:model="editText" class="form-control" rows="4"></textarea>
                                @error('editText') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" wire:click="cancelEdit">Abbrechen</button>
                                <button type="submit" class="btn btn-primary">Speichern</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
