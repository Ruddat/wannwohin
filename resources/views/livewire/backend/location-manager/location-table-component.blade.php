<div>
    <div class="row mb-3">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <input wire:model.debounce.300ms="search" type="text" class="form-control" placeholder="Suchen...">
        </div>
        <div class="col-lg-2 col-md-3 col-sm-6">
            <select wire:model="perPage" class="form-select">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Locations</h3>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>IATA Code</th>
                        <th>Country</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($locations as $location)
                        <tr>
                            <td>{{ $location->id }}</td>
                            <td>{{ $location->title }}</td>
                            <td>{{ $location->iata_code }}</td>
                            <td>{{ $location->country->title ?? 'N/A' }}</td>
                            <td class="text-end">
                                <a href="{{ route('verwaltung.location-table-manager.edit', ['locationId' => $location->id]) }}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-edit"></i> Bearbeiten
                                </a>



<button
    type="button"
    wire:click="deleteLocation({{ $location->id }})"
    wire:confirm="Are you sure you want to delete this City?"
>
    Delete Location
</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Keine Locations gefunden.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $locations->links('pagination::bootstrap-5') }}
    </div>
</div>

