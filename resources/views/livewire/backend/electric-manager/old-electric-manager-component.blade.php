<div>
    <div class="mb-3 d-flex justify-content-between">
        <input wire:model="search" type="text" class="form-control w-50" placeholder="Search by Country">
        <button wire:click="create" class="btn btn-primary">Add New</button>
    </div>

    @if ($editMode)
        @include('livewire.backend.electric-manager.edit-form')
    @else
        @include('livewire.backend.electric-manager.create-form')
    @endif

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Country Name</th>
                    <th>Country Code</th>
                    <th>Power</th>
                    <th>Type</th>
                    <th>Actions</th>
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
                        <td>
                            <button wire:click="edit({{ $standard->id }})" class="btn btn-sm btn-warning">Edit</button>
                            <button wire:click="delete({{ $standard->id }})" class="btn btn-sm btn-danger">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $standards->links() }}
    </div>
</div>
