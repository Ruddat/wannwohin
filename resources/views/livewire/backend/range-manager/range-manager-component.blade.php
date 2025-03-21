<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row m-1">
        <div class="col-12">
            <h5>Range Management</h5>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a href="#" class="f-s-14 f-w-500">
                        <span><i class="ph-duotone ph-list-numbers f-s-16"></i> Ranges</span>
                    </a>
                </li>
                <li class="active">
                    <a href="#" class="f-s-14 f-w-500">Verwalten</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Table Section -->
    <div class="row table-section">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5>Ranges</h5>
                    <p>Verwalte Preisspannen für Flüge, Hotels, Mietwagen und Reisen</p>
                </div>

                <div class="card-body border-bottom py-3">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-8 mb-2 mb-md-0">
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <div class="d-flex align-items-center">
                                    <label class="me-2 text-secondary f-w-500" for="perPage">Einträge:</label>
                                    <select wire:model.change="perPage" id="perPage" class="form-select form-select-sm w-auto">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                                <div class="d-flex align-items-center">
                                    <label class="me-2 text-secondary f-w-500" for="selectedType">Typ:</label>
                                    <select wire:model.change="selectedType" id="selectedType" class="form-select form-select-sm w-auto">
                                        <option value="">Alle Typen</option>
                                        <option value="Flight">Flight</option>
                                        <option value="Hotel">Hotel</option>
                                        <option value="Rental">Rental</option>
                                        <option value="Travel">Travel</option>
                                    </select>
                                </div>
                                <div class="d-flex align-items-center flex-grow-1">
                                    <div class="input-icon w-100 position-relative" style="max-width: 300px;">
                                        <input wire:model.live="search" type="text" class="form-control form-control-sm" placeholder="Suchen...">
                                        <span class="input-icon-addon position-absolute end-0 top-50 translate-middle-y pe-2">
                                            <i class="ph-duotone ph-magnifying-glass f-s-16"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 text-md-end">
                            <button wire:click="create" class="btn text-bg-primary border-0">Neu</button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Sortierung</th>
                                    <th scope="col">Range</th>
                                    <th scope="col">Typ</th>
                                    <th scope="col" class="text-end">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ranges as $range)
                                    <tr>
                                        <td>{{ $range->id }}</td>
                                        <td class="f-w-500">{{ $range->sort }}</td>
                                        <td>{{ $range->range_to_show }}</td>
                                        <td class="text-secondary f-w-600">{{ $range->type }}</td>
                                        <td class="text-end">
                                            <button class="btn btn-sm text-bg-primary border-0" wire:click="edit({{ $range->id }})">
                                                Bearbeiten
                                            </button>
                                            <button class="btn btn-sm text-bg-danger border-0" wire:click="delete({{ $range->id }})">
                                                Löschen
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Keine Ranges gefunden.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

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
                                    <li class="page-item {{ $ranges->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link b-r-left" wire:click="previousPage" href="#" aria-label="Previous">
                                            Previous
                                        </a>
                                    </li>
                                    @php
                                        $currentPage = $ranges->currentPage();
                                        $lastPage = $ranges->lastPage();
                                        $range = 2;
                                        $start = max(1, $currentPage - $range);
                                        $end = min($lastPage, $currentPage + $range);
                                    @endphp
                                    @for ($i = $start; $i <= $end; $i++)
                                        <li class="page-item {{ $currentPage == $i ? 'active' : '' }}" aria-current="{{ $currentPage == $i ? 'page' : '' }}">
                                            <a class="page-link" wire:click="gotoPage({{ $i }})" href="#">{{ $i }}</a>
                                        </li>
                                    @endfor
                                    <li class="page-item {{ $ranges->hasMorePages() ? '' : 'disabled' }}">
                                        <a class="page-link b-r-right" wire:click="nextPage" href="#" aria-label="Next">
                                            Next
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <span class="text-muted">
                                Zeigt {{ $ranges->firstItem() }} bis {{ $ranges->lastItem() }} von {{ $ranges->total() }} Einträgen
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Neues Modal -->
    @if($showForm)
        <div class="modal fade show" id="rangeModal" tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog app_modal_sm">
                <div class="modal-content">
                    <div class="modal-header bg-primary-800">
                        <h1 class="modal-title fs-5 text-white">{{ $editMode ? 'Range bearbeiten' : 'Neuen Range erstellen' }}</h1>
                        <button type="button" class="fs-5 border-0 bg-none text-white" wire:click="resetInputFields" aria-label="Close">
                            <i class="fa-solid fa-xmark fs-3"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="mb-3">
                                <label for="sort" class="form-label f-w-500">Sortierung</label>
                                <input type="number" id="sort" wire:model="sort" class="form-control">
                                @error('sort') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="range_to_show" class="form-label f-w-500">Range</label>
                                <input type="text" id="range_to_show" wire:model="range_to_show" class="form-control" placeholder="z. B. 100€ oder >1.000€">
                                @error('range_to_show') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label f-w-500">Typ</label>
                                <select id="type" wire:model="type" class="form-select">
                                    <option value="">Typ wählen</option>
                                    <option value="Flight">Flight</option>
                                    <option value="Hotel">Hotel</option>
                                    <option value="Rental">Rental</option>
                                    <option value="Travel">Travel</option>
                                </select>
                                @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary" wire:click="resetInputFields">Abbrechen</button>
                        <button type="button" class="btn btn-light-primary" wire:click="save">Speichern</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
