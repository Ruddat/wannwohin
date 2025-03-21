<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <!-- Header mit Suche und Add-Button -->
            <div class="card-header border-bottom-0 bg-light">
                <h3 class="card-title fw-bold">Electric Standards</h3>
                <div class="ms-auto d-flex align-items-center">
                    <div class="input-icon me-2">
                        <input wire:model.live="search" type="text" class="form-control" placeholder="Search by Country">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                    </div>
                    <button wire:click="create" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i> Add New
                    </button>
                </div>
            </div>

            <!-- Formular nur anzeigen, wenn $showForm true ist -->
            @if ($showForm)
                <div class="card-body">
                    @if ($editMode)
                        @include('livewire.backend.electric-manager.edit-form')
                    @else
                        @include('livewire.backend.electric-manager.create-form')
                    @endif
                </div>
            @endif

            <!-- Tabelle -->
            @if (!$showForm)
                <div class="table-responsive">
                    <table class="table table-vcenter table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Country Name</th>
                                <th>Country Code</th>
                                <th>Power</th>
                                <th>Plug Types</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($standards as $standard)
                                <tr>
                                    <td>{{ $standard->country_name }}</td>
                                    <td>{{ $standard->country_code }}</td>
                                    <td>{{ $standard->power }}</td>
                                    <td>
                                        @foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'] as $type)
                                            @if ($standard->{'typ_' . strtolower($type)})
                                                <span class="badge bg-info-lt me-1">{{ $type }}</span>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-list flex-nowrap">
                                            <button wire:click="edit({{ $standard->id }})" class="btn btn-icon btn-warning btn-sm" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            <button wire:click="delete({{ $standard->id }})" class="btn btn-icon btn-danger btn-sm" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
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
                                    <li class="page-item {{ $standards->onFirstPage() ? 'disabled' : '' }}">
                                        <a class="page-link b-r-left" wire:click="previousPage" href="#" aria-label="Previous">
                                            Previous
                                        </a>
                                    </li>
                                    @php
                                        $currentPage = $standards->currentPage();
                                        $lastPage = $standards->lastPage();
                                        $range = 2;
                                        $start = max(1, $currentPage - $range);
                                        $end = min($lastPage, $currentPage + $range);
                                    @endphp
                                    @for ($i = $start; $i <= $end; $i++)
                                        <li class="page-item {{ $currentPage == $i ? 'active' : '' }}" aria-current="{{ $currentPage == $i ? 'page' : '' }}">
                                            <a class="page-link" wire:click="gotoPage({{ $i }})" href="#">{{ $i }}</a>
                                        </li>
                                    @endfor
                                    <li class="page-item {{ $standards->hasMorePages() ? '' : 'disabled' }}">
                                        <a class="page-link b-r-right" wire:click="nextPage" href="#" aria-label="Next">
                                            Next
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <span class="text-muted">
                                Zeigt {{ $standards->firstItem() }} bis {{ $standards->lastItem() }} von {{ $standards->total() }} Einträgen
                            </span>
                        </div>
                    </div>
                </div>

            @endif
        </div>
    </div>
</div>
