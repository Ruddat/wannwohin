<div>
    <div class="container-fluid">
        <!-- Breadcrumb start -->
        <div class="row m-1">
            <div class="col-12">
                <h5>Weather Stations</h5>
                <ul class="app-line-breadcrumbs mb-3">
                    <li>
                        <a href="#" class="f-s-14 f-w-500">
                            <span><i class="ph-duotone ph-table f-s-16"></i> Tables</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="#" class="f-s-14 f-w-500">Weather Stations</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Breadcrumb end -->

        <!-- Message -->
        @if (session()->has('message'))
            <div class="alert alert-success mb-3">
                {{ session('message') }}
            </div>
        @endif

        <!-- Search -->
        <div class="mb-3">
            <input wire:model="search" type="text" class="form-control" placeholder="Search stations...">
        </div>

        <!-- Table start -->
        <div class="row table-section">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Weather Stations</h5>
                        <p>Manage your weather station data efficiently</p>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Station ID</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Country</th>
                                        <th scope="col">Region</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stations as $station)
                                        <tr>
                                            <td>{{ $station->id }}</td>
                                            <td>{{ $station->station_id }}</td>
                                            <td class="f-w-500">{{ $station->name }}</td>
                                            <td class="text-secondary f-w-600">{{ $station->country }}</td>
                                            <td>{{ $station->region }}</td>
                                            <td>
                                                <button wire:click="editStation({{ $station->id }})"
                                                        class="btn btn-sm text-bg-warning border-0">Edit</button>
                                                <button wire:click="deleteStation({{ $station->id }})"
                                                        class="btn btn-sm text-bg-danger border-0">Delete</button>
                                            </td>
                                        </tr>
                                    @endforeach
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
                                        <li class="page-item {{ $stations->onFirstPage() ? 'disabled' : '' }}">
                                            <a class="page-link b-r-left" wire:click="previousPage" href="#"
                                               aria-label="Previous">Previous</a>
                                        </li>
                                        @php
                                            $currentPage = $stations->currentPage();
                                            $lastPage = $stations->lastPage();
                                            $range = 2;
                                            $start = max(1, $currentPage - $range);
                                            $end = min($lastPage, $currentPage + $range);
                                        @endphp
                                        @for ($i = $start; $i <= $end; $i++)
                                            <li class="page-item {{ $currentPage == $i ? 'active' : '' }}"
                                                aria-current="{{ $currentPage == $i ? 'page' : '' }}">
                                                <a class="page-link" wire:click="gotoPage({{ $i }})" href="#">{{ $i }}</a>
                                            </li>
                                        @endfor
                                        <li class="page-item {{ $stations->hasMorePages() ? '' : 'disabled' }}">
                                            <a class="page-link b-r-right" wire:click="nextPage" href="#"
                                               aria-label="Next">Next</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-3 text-end">
                                <span class="text-muted">
                                    Zeigt {{ $stations->firstItem() }} bis {{ $stations->lastItem() }} von {{ $stations->total() }} Einträgen
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Table end -->

        <!-- Add/Edit Modal -->
        @if ($showForm)
            <div class="modal show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5);">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="card-header">
                            <h5>{{ $editMode ? 'Edit Weather Station' : 'Add Weather Station' }}</h5>
                            <button type="button" wire:click="resetFields" class="btn-close"></button>
                        </div>
                        <form wire:submit.prevent="{{ $editMode ? 'updateStation' : 'addStation' }}">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Station ID</label>
                                    <input type="text" wire:model="stationId" class="form-control">
                                    @error('stationId') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" wire:model="name" class="form-control">
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" wire:model="country" class="form-control">
                                    @error('country') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Region</label>
                                    <input type="text" wire:model="region" class="form-control">
                                    @error('region') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Latitude</label>
                                    <input type="number" step="0.0001" wire:model="latitude" class="form-control">
                                    @error('latitude') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Longitude</label>
                                    <input type="number" step="0.0001" wire:model="longitude" class="form-control">
                                    @error('longitude') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Elevation</label>
                                    <input type="number" step="0.01" wire:model="elevation" class="form-control">
                                    @error('elevation') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Timezone</label>
                                    <input type="text" wire:model="timezone" class="form-control">
                                    @error('timezone') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Inventory (JSON)</label>
                                    <textarea wire:model="inventory" class="form-control"></textarea>
                                    @error('inventory') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn text-bg-primary border-0">{{ $editMode ? 'Update' : 'Add' }}</button>
                                <button type="button" wire:click="resetFields" class="btn text-bg-secondary border-0">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
