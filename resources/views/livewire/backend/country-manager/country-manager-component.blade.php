<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <!-- Card -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Countries</h3>
                    </div>

                    <div class="card-body border-bottom py-3">
                        <div class="d-flex">
                            <div class="text-secondary"></div>
                            <div class="ms-auto text-secondary">
                                Search:
                                <div class="ms-2 d-inline-block">
                                    <input wire:model.live="search" type="text" class="form-control form-control-sm" aria-label="Search invoice" spellcheck="false" placeholder="Search countries...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover card-table table-vcenter">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Continent</th>
                                    <th>Code</th>
                                    <th>Population</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($countries as $country)
                                    <tr>
                                        <td>{{ $country->id }}</td>
                                        <td>{{ $country->title }}</td>
                                        <td>{{ $country->continent->title ?? 'N/A' }}</td>
                                        <td>{{ $country->country_code }}</td>
                                        <td>{{ $country->population ?? 'N/A' }}</td>
                                        <td>
                                            <button wire:click="toggleStatus({{ $country->id }})"
                                                class="btn btn-sm @if ($country->status === 'active') btn-success @elseif ($country->status === 'pending') btn-warning @else btn-secondary @endif">
                                                {{ ucfirst($country->status) }}
                                            </button>
                                        </td>
                                        <td>
                                            <button wire:click="edit({{ $country->id }})" class="btn btn-icon btn-primary btn-sm" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            <button wire:click="delete({{ $country->id }})" class="btn btn-icon btn-danger btn-sm" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No countries found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $countries->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Neues Formular zum Importieren von Ländern -->
        <div class="row row-cards mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Import Countries</h3>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('countries.import') }}" method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
                            @csrf
                            <div class="mb-3">
                                <label for="excel_file" class="form-label">Upload Excel File</label>
                                <input type="file" name="excel_file" id="excel_file" class="form-control" required>
                                @error('excel_file') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit/Create Modal -->
    <div>
        @if ($editMode)
            <div class="modal fade show d-block" style="background-color: rgba(0, 0, 0, 0.5);">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $countryId ? 'Edit Country' : 'Create Country' }}</h5>
                            <button type="button" class="btn-close" wire:click="resetInputFields"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input wire:model="title" type="text" id="title" class="form-control" placeholder="Country Title">
                                @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Continent -->
                            <div class="mb-3">
                                <label for="continent_id" class="form-label">Continent</label>
                                <select wire:model="continent_id" id="continent_id" class="form-select">
                                    <option value="">Select Continent</option>
                                    @foreach ($continents as $continent)
                                        <option value="{{ $continent->id }}">{{ $continent->title }}</option>
                                    @endforeach
                                </select>
                                @error('continent_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <!-- Country Code -->
                            <div class="mb-3">
                                <label for="country_code" class="form-label">Country Code</label>
                                <input wire:model="country_code" type="text" id="country_code" class="form-control" placeholder="Country Code (e.g., US)">
                                @error('country_code') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <livewire:jodit-text-editor wire:model.live="country_text" :buttons="['bold', 'italic', 'underline', 'strikeThrough', '|', 'left', 'center', 'right', '|', 'link', 'image']" />
                            </div>

                            <livewire:jodit-text-editor wire:model.live="content" :buttons="['bold', 'italic', 'underline', 'strikeThrough', '|', 'left', 'center', 'right', '|', 'link', 'image']" />




                            <!-- Custom Images -->
                            <div class="mb-3">
                                <label for="custom_images" class="form-label">Enable Custom Images</label>
                                <input wire:model="custom_images" type="checkbox" id="custom_images">
                                <span>Use custom image uploads instead of Pixabay images</span>
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
                            <button wire:click="save" class="btn btn-primary">Save</button>
                            <button wire:click="resetInputFields" class="btn btn-secondary">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

<!-- Include Jodit CSS Styling -->
<link rel="stylesheet" href="//unpkg.com/jodit@4.1.16/es2021/jodit.min.css">

<!-- Include the Jodit JS Library -->
<script src="//unpkg.com/jodit@4.1.16/es2021/jodit.min.js"></script>

</div>
</div>
