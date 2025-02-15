<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Location Manager <span class="text-muted">/ Standort bearbeiten </span>
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">


                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Locations</h3>
                        </div>

                        <!-- Filter und Suche -->
                        <div class="card-body border-bottom py-3">
                            <div class="row align-items-center g-3">
                                <!-- Anzahl pro Seite -->
                                <div class="col-auto">
                                    <label class="form-label text-muted">Show</label>
                                    <select wire:model.change="perPage" class="form-select form-select-sm">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>

                                <!-- Suche -->
                                <div class="col-md-auto ms-auto">
                                    <label class="form-label text-muted">Search</label>
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-search"></i>
                                        </span>
                                        <input wire:model.live="search" type="text"
                                            class="form-control form-control-sm" placeholder="Search...">
                                    </div>
                                </div>

                                <!-- Länderfilter -->
                                <div class="col-md-auto ms-auto">
                                    <label class="form-label text-muted">Country</label>
                                    <select wire:model.change="filterCountry" class="form-select form-select-sm">
                                        <option value="">All Countries</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Statusfilter -->
                                <div class="col-md-auto ms-auto">
                                    <label class="form-label text-muted">Status</label>
                                    <select wire:model.change="filterStatus" class="form-select form-select-sm">
                                        <option value="">All Status</option>
                                        <option value="active">Active</option>
                                        <option value="pending">Pending</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>

                                <!-- Soft-Delete-Filter -->
                                <div class="col-md-auto ms-auto">
                                    <label class="form-label text-muted">Deleted Locations</label>
                                    <select wire:model.change="filterDeleted" class="form-select form-select-sm">
                                        <option value="">Active Only</option>
                                        <option value="only_deleted">Only Deleted</option>
                                        <option value="with_deleted">All (Including Deleted)</option>
                                    </select>
                                </div>

                                <div class="col-md-auto ms-auto">
                                    <label class="form-label d-none d-md-block">&nbsp;</label>
                                    <button wire:click="exportLocations" class="btn btn-success btn-sm">
                                        <i class="ti ti-file-export"></i> Export to Excel
                                    </button>
                                </div>


                                <!-- Reset-Button -->
                                <div class="col-md-auto ms-auto">
                                    <label class="form-label d-none d-md-block">&nbsp;</label>
                                    <button wire:click="resetFilters" class="btn btn-secondary btn-sm">
                                        <i class="ti ti-refresh"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>



                        <!-- Tabelle -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th wire:click="sortBy('id')" style="cursor: pointer;">ID</th>
                                        <th wire:click="sortBy('title')" style="cursor: pointer;">Title</th>
                                        <th>IATA Code</th>
                                        <th wire:click="sortBy('country')" style="cursor: pointer;">Country</th>
                                        <th wire:click="sortBy('status')" style="cursor: pointer;">Status</th>
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
                                                        class="text-decoration-none" target="_blank"
                                                        rel="noopener noreferrer">
                                                        {{ $location->title }} <i class="ti ti-external-link"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">{{ $location->title }} (Unbekannte
                                                        Daten)</span>
                                                @endif
                                            </td>
                                            <td>{{ $location->iata_code }}</td>
                                            <td>
                                                @if ($location->country)
                                                    <a href="#"
                                                        wire:click.prevent="$set('filterCountry', {{ $location->country->id }})">
                                                        {{ $location->country->title }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span wire:click="toggleStatus({{ $location->id }})"
                                                    class="badge bg-{{ $location->status === 'active' ? 'success' : ($location->status === 'pending' ? 'warning' : 'secondary') }}"
                                                    style="cursor: pointer;">
                                                    {{ ucfirst($location->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                @if ($location->deleted_at)
                                                    <button type="button" wire:click="restoreLocation({{ $location->id }})"
                                                        class="btn btn-sm btn-warning">
                                                        <i class="ti ti-arrow-back"></i> Restore
                                                    </button>

                                                    <button type="button" onclick="confirmForceDelete({{ $location->id }})"
                                                        class="btn btn-sm btn-danger">
                                                        <i class="ti ti-trash"></i> Permanently Delete
                                                    </button>
                                                @else
                                                    <a href="{{ route('verwaltung.location-table-manager.edit', ['locationId' => $location->id]) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="ti ti-edit"></i> Edit
                                                    </a>
                                                    <button type="button" wire:click="confirmDelete({{ $location->id }})"
                                                        class="btn btn-sm btn-danger">
                                                        <i class="ti ti-trash"></i> Delete
                                                    </button>
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
                            {{ $locations->links() }}
                        </div>
                    </div>
                </div>


                <!-- Import Locations Form -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Import Locations</h3>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('locations.import') }}" method="POST" enctype="multipart/form-data"
                            class="bg-white p-4 rounded shadow">
                            @csrf
                            <!-- Datei-Upload -->
                            <div class="mb-3">
                                <label for="excel_file" class="form-label">Upload Excel File</label>
                                <input type="file" name="excel_file" id="excel_file" class="form-control"
                                    required>
                            </div>

                            <!-- Option: Bilder überspringen -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="skip_images" id="skip_images">
                                <label class="form-check-label" for="skip_images">
                                    Skip importing images
                                </label>
                            </div>

                            <!-- Option: Fehlgeschlagene Zeilen exportieren -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="export_failed"
                                    id="export_failed">
                                <label class="form-check-label" for="export_failed">
                                    Export failed rows to Excel
                                </label>
                            </div>

                            <!-- Import-Button -->
                            <button type="submit" class="btn btn-primary">Import</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


@assets
    <style>
        .table a {
            color: #007bff;
            text-decoration: none;
        }

        .table a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
@endassets
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
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

        Livewire.on('showSuccessMessage', (message) => {
            Swal.fire({
                title: 'Erfolgreich!',
                text: message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        });

        window.confirmForceDelete = function (locationId) {
            Swal.fire({
                title: 'Endgültig löschen?',
                text: 'Diese Location wird dauerhaft entfernt und kann nicht wiederhergestellt werden!',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ja, endgültig löschen!',
                cancelButtonText: 'Abbrechen'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('forceDeleteConfirmed', locationId);
                }
            });
        };
    });
</script>
@endpush

