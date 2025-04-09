<div class="container-fluid">
    <!-- Breadcrumb start -->
    <div class="row m-1">
        <div class="col-12">
            <h5>Übersetzungen verwalten</h5>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a href="#" class="f-s-14 f-w-500">
                        <span><i class="ph-duotone ph-translate f-s-16"></i> Übersetzungen</span>
                    </a>
                </li>
                <li class="active">
                    <a href="#" class="f-s-14 f-w-500">Verwalten</a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Breadcrumb end -->

    <!-- Table section start -->
    <div class="row table-section">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5>Übersetzungen</h5>
                    <p>Verwalte deine Übersetzungen effizient</p>
                </div>

                <div class="card-body border-bottom py-3">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center">
                        <div class="text-secondary mb-3 mb-sm-0">
                            <select wire:model.change="perPage" class="form-select form-select-sm w-auto">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div class="ms-sm-auto d-flex align-items-center">
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
                                    <i class="ph-duotone ph-magnifying-glass f-s-16"></i>
                                </span>
                                <input wire:model.live="search" type="text" class="form-control form-control-sm" placeholder="Suchen...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Schlüssel</th>
                                    <th scope="col">Sprache</th>
                                    <th scope="col">Text</th>
                                    <th scope="col" class="text-end">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($translations as $translation)
                                    <tr>
                                        <td class="f-w-500">{{ $translation->key }}</td>
                                        <td class="text-secondary f-w-600">{{ $translation->locale }}</td>
                                        <td>{{ Str::limit($translation->text, 50) }}</td>
                                        <td class="text-end">
                                            <button class="btn btn-sm text-bg-primary border-0" wire:click="edit({{ $translation->id }})">
                                                Bearbeiten
                                            </button>
                                            <button class="btn btn-sm text-bg-danger border-0" wire:click="delete({{ $translation->id }})">
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


                {{ $translations->links('vendor.livewire.custom-pagination') }}



            </div>
        </div>
    </div>
    <!-- Table section end -->

    <!-- Modal -->
    @if($editingTranslation)
        <div class="modal show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="card-header">
                        <h5>Übersetzung bearbeiten</h5>
                        <button type="button" class="btn-close" wire:click="cancelEdit"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="updateTranslation">
                            <div class="mb-3">
                                <label for="editKey" class="form-label f-w-500">Schlüssel</label>
                                <input type="text" id="editKey" wire:model="editKey" class="form-control" disabled>
                                @error('editKey') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="editLocale" class="form-label f-w-500">Sprache</label>
                                <input type="text" id="editLocale" wire:model="editLocale" class="form-control" disabled>
                                @error('editLocale') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="editText" class="form-label f-w-500">Text</label>
                                <textarea id="editText" wire:model="editText" class="form-control" rows="4"></textarea>
                                @error('editText') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn text-bg-secondary border-0" wire:click="cancelEdit">Abbrechen</button>
                                <button type="submit" class="btn text-bg-primary border-0">Speichern</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
