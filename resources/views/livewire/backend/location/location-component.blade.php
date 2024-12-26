<div>
    <!-- Search and Filter -->
    <div class="d-flex justify-content-between mb-3">
        <!-- Search Input -->
        <div class="input-group">
            <input type="text" wire:model.live.debounce.250ms="search" class="form-control" placeholder="Search by title or alias">
        </div>

        <!-- Status Filter -->
        <div class="input-group me-3">
            <select wire:model="filterStatus" class="form-select">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <!-- Per Page Dropdown -->
        <div class="input-group">
            <select wire:model.change="perPage" class="form-select">
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
                <option value="100">100 per page</option>
                <option value="all">All</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Alias</th>
                    <th>Thumbnails</th>
                    <th>IATA Code</th>
                    <th>Flight Hours</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locations as $location)
                    <tr>
                        <td>{{ $location->id }}</td>
                        <td>{{ $location->title }}</td>
                        <td>{{ $location->alias }}</td>
                        <td>
                            <div class="d-flex">
                                @if($location->text_pic1)
                                    <div class="thumbnail-container">
                                        <img src="{{ $location->text_pic1 }}" alt="Thumbnail 1" class="img-thumbnail">
                                    </div>
                                @endif
                                @if($location->text_pic2)
                                    <div class="thumbnail-container">
                                        <img src="{{ $location->text_pic2 }}" alt="Thumbnail 2" class="img-thumbnail">
                                    </div>
                                @endif
                                @if($location->text_pic3)
                                    <div class="thumbnail-container">
                                        <img src="{{ $location->text_pic3 }}" alt="Thumbnail 3" class="img-thumbnail">
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>{{ $location->iata_code }}</td>
                        <td>{{ $location->flight_hours }}</td>
                        <td>
                            <button wire:click="toggleStatus({{ $location->id }})" class="badge border-0
                                {{ $location->status === 'active' ? 'bg-success-lt' : ($location->status === 'pending' ? 'bg-warning-lt' : 'bg-danger-lt') }}">
                                {{ ucfirst($location->status) }}
                            </button>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('location-manager.locations.edit', $location->id) }}" class="btn btn-warning btn-sm">
                                <i class="ti ti-edit"></i> Edit
                            </a>
                            <button wire:click="confirmDelete({{ $location->id }})" class="btn btn-danger btn-sm">
                                <i class="ti ti-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No locations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($perPage !== 'all')
        {{ $locations->links() }}
    @endif

    <style>
        .thumbnail-container {
            position: relative;
            display: inline-block;
            margin-right: 10px;
        }

        .thumbnail-container img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            z-index: 1;
        }

        .thumbnail-container:hover img {
            transform: scale(5);
            position: relative;
            top: 0;
            left: 0;
            z-index: 999;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            background: white;
            border: 1px solid #fff;
        }
    </style>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('swal', (event) => {
            const data = event;
            swal.fire({
                icon: data[0]['icon'],
                title: data[0]['title'],
                text: data[0]['text'],
            });
        });

        @this.on('delete-prompt', (event) => {
            swal.fire({
                title: 'Are you sure?',
                text: 'You are about to delete this record, this action is irreversible',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.dispatch('goOn-Delete');
                    @this.on('deleted', (event) => {
                        swal.fire({
                            title: 'Deleted',
                            text: 'Your record has been deleted',
                            icon: 'success',
                        });
                    });
                }
            });
        });
    });
</script>
