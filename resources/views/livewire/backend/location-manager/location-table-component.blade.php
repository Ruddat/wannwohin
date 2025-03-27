<div class="container-fluid">
    <!-- Breadcrumb start -->
    <div class="row m-1">
        <div class="col-12 ">
          <h4 class="main-title">Blank</h4>
          <ul class="app-line-breadcrumbs mb-3">
            <li class="">
              <a href="#" class="f-s-14 f-w-500">
                <span>
                  <i class="ph-duotone  ph-newspaper f-s-16"></i> Other Pages
                </span>
              </a>
            </li>
            <li class="active">
              <a href="#" class="f-s-14 f-w-500">Blank</a>
            </li>
          </ul>
        </div>
      </div>
      <!-- Breadcrumb end -->

<div class="row">
    <!-- Page Header -->
    <div class="col-md-12">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Location Manager <span class="text-muted">/ Standorte verwalten</span></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Location Table -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Locations</h3>
                <button wire:click="$dispatch('openCreateModal')" class="btn btn-sm btn-primary">
                    Standort erstellen
                  </button>
            </div>
            <div class="card-body border-bottom py-3">
                <div class="row align-items-center g-3">
                    <div class="col-auto">
                        <label class="form-label text-muted">Show</label>
                        <select wire:model.change="perPage" class="form-select form-select-sm">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div class="col-md-auto ms-auto">
                        <label class="form-label text-muted">Search</label>
                        <input wire:model.live="search" type="text" class="form-control form-control-sm" placeholder="Search...">
                    </div>
                    <div class="col-md-auto">
                        <label class="form-label text-muted">Country</label>
                        <select wire:model.change="filterCountry" class="form-select form-select-sm">
                            <option value="">All Countries</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <label class="form-label text-muted">Status</label>
                        <select wire:model.change="filterStatus" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <label class="form-label text-muted">Deleted Locations</label>
                        <select wire:model.change="filterDeleted" class="form-select form-select-sm">
                            <option value="">Active Only</option>
                            <option value="only_deleted">Only Deleted</option>
                            <option value="with_deleted">All (Including Deleted)</option>
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <label class="form-label d-none d-md-block"> </label>
                        <button wire:click="exportLocations" class="btn btn-success btn-sm">Export to Excel</button>
                    </div>
                    <div class="col-md-auto">
                        <label class="form-label d-none d-md-block"> </label>
                        <button wire:click="resetFilters" class="btn btn-secondary btn-sm">Reset</button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('id')" style="cursor: pointer;">
                                ID
                                @if($sortField === 'id')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span>↕</span>
                                @endif
                            </th>
                            <th wire:click="sortBy('title')" style="cursor: pointer;">
                                Title
                                @if($sortField === 'title')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span>↕</span>
                                @endif
                            </th>
                            <th wire:click="sortBy('iata_code')" style="cursor: pointer;">
                                IATA Code
                                @if($sortField === 'iata_code')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span>↕</span>
                                @endif
                            </th>
                            <th wire:click="sortBy('country')" style="cursor: pointer;">
                                Country
                                @if($sortField === 'country')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span>↕</span>
                                @endif
                            </th>
                            <th wire:click="sortBy('status')" style="cursor: pointer;">
                                Status
                                @if($sortField === 'status')
                                    <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                                @else
                                    <span>↕</span>
                                @endif
                            </th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($locations as $location)
                            <tr>
                                <td>{{ $location->id }}</td>
                                <td>
                                    @if ($location->country?->continent?->alias && $location->country?->alias && $location->alias)
                                        <a href="{{ route('location.details', [
                                            'continent' => $location->country->continent->alias,
                                            'country' => $location->country->alias,
                                            'location' => $location->alias,
                                        ]) }}"
                                            class="text-decoration-none" target="_blank" rel="noopener noreferrer">
                                            {{ $location->title }} <i class="ti ti-external-link"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">{{ $location->title }} (Unbekannte Daten)</span>
                                    @endif
                                </td>
                                <td>{{ $location->iata_code }}</td>
                                <td>{{ $location->country->title ?? 'N/A' }}</td>
                                <td>
                                    <span wire:click="toggleStatus({{ $location->id }})" class="badge bg-{{ $location->status === 'active' ? 'success' : ($location->status === 'pending' ? 'warning' : 'secondary') }}" style="cursor: pointer;">
                                        {{ ucfirst($location->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if ($location->deleted_at)
                                        <button wire:click="restoreLocation({{ $location->id }})" class="btn btn-sm btn-warning">Restore</button>
                                        <button onclick="confirmForceDelete({{ $location->id }})" class="btn btn-sm btn-danger">Permanently Delete</button>
                                    @else
                                        <button wire:click="openEditModal({{ $location->id }})" class="btn btn-sm btn-primary">Edit</button>
                                        <button wire:click="confirmDelete({{ $location->id }})" class="btn btn-sm btn-danger">Delete</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No Locations Found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
                                <li class="page-item {{ $locations->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link b-r-left" wire:click="previousPage" href="#" aria-label="Previous">
                                        Previous
                                    </a>
                                </li>
                                @php
                                    $currentPage = $locations->currentPage();
                                    $lastPage = $locations->lastPage();
                                    $range = 2;
                                    $start = max(1, $currentPage - $range);
                                    $end = min($lastPage, $currentPage + $range);
                                @endphp
                                @for ($i = $start; $i <= $end; $i++)
                                    <li class="page-item {{ $currentPage == $i ? 'active' : '' }}" aria-current="{{ $currentPage == $i ? 'page' : '' }}">
                                        <a class="page-link" wire:click="gotoPage({{ $i }})" href="#">{{ $i }}</a>
                                    </li>
                                @endfor
                                <li class="page-item {{ $locations->hasMorePages() ? '' : 'disabled' }}">
                                    <a class="page-link b-r-right" wire:click="nextPage" href="#" aria-label="Next">
                                        Next
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-3 text-end">
                        <span class="text-muted">
                            Zeigt {{ $locations->firstItem() }} bis {{ $locations->lastItem() }} von {{ $locations->total() }} Einträgen
                        </span>
                    </div>
                </div>
            </div>



        </div>
    </div>

    <!-- Import Locations Form -->

    <!-- Import Locations Form -->
<div class="col-12 mt-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Import Locations</h3>
        </div>
        <div class="card-body">
            <!-- Erfolgs- und Fehlermeldungen werden über Livewire-Events gehandhabt, keine Session-Variablen nötig -->
            <form wire:submit.prevent="importLocations" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
                <div class="mb-3">
                    <label for="excel_file" class="form-label">Upload Excel File</label>
                    <input type="file" wire:model="excelFile" id="excel_file" class="form-control" accept=".xlsx, .xls" required>
                    @error('excelFile')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
               {{--
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" wire:model="skipImages" id="skip_images">
                    <label class="form-check-label" for="skip_images">Skip importing images</label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" wire:model="exportFailed" id="export_failed">
                    <label class="form-check-label" for="export_failed">Export failed rows to Excel</label>
                </div>
                --}}
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading wire:target="importLocations">Importing...</span>
                    <span wire:loading.remove wire:target="importLocations">Import</span>
                </button>
            </form>
        </div>
    </div>
</div>

@livewire('backend.location-manager.location-create-component')

{{--
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Import Locations</h3>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <form action="{{ route('locations.import') }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
                    @csrf
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">Upload Excel File</label>
                        <input type="file" name="excel_file" id="excel_file" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="skip_images" id="skip_images">
                        <label class="form-check-label" for="skip_images">Skip importing images</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="export_failed" id="export_failed">
                        <label class="form-check-label" for="export_failed">Export failed rows to Excel</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Import</button>
                </form>
            </div>
        </div>
    </div>
    --}}

    <!-- Einbettung der LocationManagerComponent -->
    <div class="col-12">
        @livewire('backend.location-manager.location-manager-component')
    </div>
</div>

</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Livewire.on('showSuccessMessage', (message) => {
                Swal.fire({
                    title: 'Erfolgreich!',
                    text: message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            Livewire.on('showErrorMessage', (message) => {
                Swal.fire({
                    title: 'Fehler!',
                    text: message,
                    icon: 'error',
                    timer: 3000,
                    showConfirmButton: false
                });
            });

            // Bestehender Code für Delete-Bestätigungen etc. bleibt erhalten
            Livewire.on('triggerDeleteConfirmation', (locationId) => {
                Swal.fire({
                    title: 'Bist du sicher?',
                    text: 'Diese Aktion kann nicht rückgängig gemacht werden!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ja, löschen!',
                    cancelButtonText: 'Abbrechen'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('deleteConfirmed', locationId);
                    }
                });
            });

            window.confirmForceDelete = function (locationId) {
    Swal.fire({
        title: 'Endgültig löschen?',
        text: 'Diese Location wird dauerhaft entfernt!',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ja, endgültig löschen!',
        cancelButtonText: 'Abbrechen'
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.dispatch('forceDeleteConfirmed', { locationId }); // ✅ hier korrekt
        }
    });
};
        });
    </script>
@endpush
