<div class="container">
    <!-- Header mit Suche und Add-Button -->
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Electric Standards</h3>
        <div class="d-flex">
            <input wire:model="search" type="text" class="form-control form-control-sm me-2" placeholder="Search by Country">
            <button wire:click="create" class="btn btn-primary btn-sm">
                <i class="ti ti-plus"></i> Add New
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
            <table class="table table-vcenter card-table">
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
                                        <span class="badge bg-info">{{ $type }}</span>
                                    @endif
                                @endforeach
                            </td>
                            <td class="text-end">
                                <button wire:click="edit({{ $standard->id }})" class="btn btn-sm btn-warning">
                                    <i class="ti ti-edit"></i> Edit
                                </button>
                                <button wire:click="delete({{ $standard->id }})" class="btn btn-sm btn-danger">
                                    <i class="ti ti-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
    <!-- Pagination -->
    <div class="card-footer d-flex align-items-center">
        {{ $standards->links() }}
    </div>
        </div>

        @endif


</div>
