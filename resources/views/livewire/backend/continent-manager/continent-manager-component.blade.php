<div class="container mt-4">
    @if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h3 class="card-title">Continents</h3>
                <p class="card-subtitle text-muted mt-1">
                    Hier können Kontinente verwaltet, bearbeitet und erstellt werden. Zusätzlich können individuelle Bilder hochgeladen oder Pixabay-Bilder verwendet werden.
                </p>
            </div>
            <div>
                <input wire:model.live="search" type="text" class="form-control form-control-sm" placeholder="Search...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table card-table table-vcenter">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Images</th>
                        <th>Header</th>
                        <th>Text</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($continents as $continent)
                        <tr>
                            <td>{{ $continent->id }}</td>
                            <td>{{ $continent->title }}</td>
                            <td>
                                <div class="d-flex">
                                    @for ($i = 1; $i <= 3; $i++)
                                        @php
                                            $imagePath = $continent["image{$i}_path"];
                                        @endphp
                                        @if ($imagePath)
                                            <img src="{{ Storage::url($imagePath) }}" alt="Image {{ $i }}" class="img-thumbnail me-2" width="50">
                                        @endif
                                    @endfor
                                </div>
                            </td>
                            <td>{!! $continent->continent_header_text !!}</td>
                            <td>{!! $continent->continent_text !!}</td>
                            <td>
                                <button wire:click="toggleStatus({{ $continent->id }})"
                                    class="btn btn-sm @if ($continent->status === 'active') btn-success @elseif ($continent->status === 'pending') btn-warning @else btn-secondary @endif">
                                    {{ ucfirst($continent->status) }}
                                </button>
                            </td>
                            <td>
                                <button wire:click="edit({{ $continent->id }})" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-pencil"></i> Edit
                                </button>
                                <button wire:click="delete({{ $continent->id }})" class="btn btn-sm btn-outline-danger">
                                    <i class="ti ti-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No continents found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between">
            {{ $continents->links() }}
        </div>
    </div>

    @if ($editMode)
        <!-- Modal for Editing -->
        <div class="modal" tabindex="-1" style="display: block;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Continent</h5>
                        <button type="button" wire:click="resetInputFields" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input wire:model="title" type="text" id="title" class="form-control" placeholder="Title">
                            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="alias" class="form-label">Alias</label>
                            <input wire:model="alias" type="text" id="alias" class="form-control" placeholder="Alias">
                            @error('alias') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="iso2" class="form-label">ISO2</label>
                                <input wire:model="iso2" type="text" id="iso2" class="form-control" placeholder="ISO2">
                                @error('iso2') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="iso3" class="form-label">ISO3</label>
                                <input wire:model="iso3" type="text" id="iso3" class="form-control" placeholder="ISO3">
                                @error('iso3') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="area_km" class="form-label">Area (km²)</label>
                                <input wire:model="area_km" type="number" id="area_km" class="form-control" placeholder="Area in km²">
                                @error('area_km') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="population" class="form-label">Population</label>
                                <input wire:model="population" type="number" id="population" class="form-control" placeholder="Population">
                                @error('population') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="continent_text" class="form-label">Continent Header Text</label>
                            <livewire:jodit-text-editor wire:model.live="continent_header_text" :buttons="['bold', 'italic', 'underline', 'strikeThrough', '|', 'left', 'center', 'right', '|', 'link', 'image']" />
                            @error('continent_header_text') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="continent_text" class="form-label">Continent Text</label>
                            <livewire:jodit-text-editor wire:model.live="continent_text" :buttons="['bold', 'italic', 'underline', 'strikeThrough', '|', 'left', 'center', 'right', '|', 'link', 'image']" />
                            @error('continent_text') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select wire:model="status" id="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="custom_images" class="form-label">Custom Images</label>
                            <input wire:model.change="custom_images" type="checkbox" id="custom_images">
                            <span>Enable custom image uploads</span>
                        </div>

                        @if ($custom_images)
                        <div class="row">
                            @for ($i = 1; $i <= 3; $i++)
                                <div class="col-md-4 mb-3 text-center">
                                    <label for="image{{ $i }}_path" class="form-label">Custom Image {{ $i }}</label>
                                    <input wire:model="image{{ $i }}_path" type="file" id="image{{ $i }}_path" class="form-control">
                                    <div class="mt-2 position-relative">
                                        @if (${"image{$i}_path"} instanceof \Livewire\TemporaryUploadedFile)
                                            <img src="{{ ${"image{$i}_path"}->temporaryUrl() }}" alt="Preview" class="img-thumbnail" style="max-width: 150px;">
                                            <button wire:click="deleteImage({{ $i }})" class="btn btn-sm btn-danger position-absolute top-0 end-0">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @error("image{$i}_path") <span class="text-danger d-block mt-1">{{ $message }}</span> @enderror
                                </div>
                            @endfor
                        </div>
                    @else
                        <div class="row">
                            @for ($i = 1; $i <= 3; $i++)
                                <div class="col-md-4 mb-3 text-center">
                                    <label>Pixabay Image {{ $i }}</label>
                                    <div class="mt-2 position-relative">
                                        @if (${"image{$i}_path"})
                                            <img src="{{ Storage::url(${"image{$i}_path"}) }}" alt="Pixabay Image {{ $i }}" class="img-thumbnail" style="max-width: 150px;">
                                            <button wire:click="deleteImage({{ $i }})" class="btn btn-sm btn-danger position-absolute top-0 end-0">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <div class="mb-3">
                            <label for="searchKeyword" class="form-label">Search Images on Pixabay</label>
                            <div class="input-group">
                                <input wire:model="searchKeyword" type="text" id="searchKeyword" class="form-control" placeholder="Enter keyword">
                                <button wire:click="fetchImagesFromPixabay" class="btn btn-outline-secondary">Search</button>
                            </div>
                        </div>

                        @if ($pixabayImages)
                            <div class="row">
                                <h5 class="text-center">Pixabay Images</h5>
                                @foreach ($pixabayImages as $index => $image)
                                    <div class="col-md-3 mb-3 text-center">
                                        <img src="{{ $image['previewURL'] }}" alt="Pixabay Image {{ $index }}" class="img-thumbnail" style="max-width: 100px;">
                                        <button wire:click="selectPixabayImage({{ $index }})" class="btn btn-sm btn-primary mt-2">
                                            Select
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif


                        @endif


                    </div>
                    <div class="modal-footer">
                        <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary">
                            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Save Changes
                        </button>
                        <button wire:click="resetInputFields" class="btn btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Include Jodit CSS Styling -->
    <link rel="stylesheet" href="//unpkg.com/jodit@4.1.16/es2021/jodit.min.css">
    <!-- Include the Jodit JS Library -->
    <script src="//unpkg.com/jodit@4.1.16/es2021/jodit.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('confirmDelete', ({ id, message }) => {
        Swal.fire({
            title: 'Confirm Deletion',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('confirmDelete', { id: id }); // Parameter als Objekt
            }
        });
    });

    Livewire.on('success', (message) => {
        Swal.fire('Success', message, 'success');
    });

    Livewire.on('error', (message) => {
        Swal.fire('Error', message, 'error');
    });
});

</script>



</div>
