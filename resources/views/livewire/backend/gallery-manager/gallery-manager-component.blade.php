<div>
    <div class="container mt-4">
        <h1>Gallery Manager</h1>

        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        <div class="mb-4">
            <input type="text" class="form-control" placeholder="Search by Location Name..." wire:model.live="searchTerm">
        </div>

        <form wire:submit.prevent="save">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="locationId" class="form-label">Location ID</label>
                        <input type="text" id="locationId" class="form-control" wire:model="locationId">
                        @error('locationId') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="locationName" class="form-label">Location Name</label>
                        <input type="text" id="locationName" class="form-control" wire:model="locationName">
                        @error('locationName') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="imagePath" class="form-label">Image Path</label>
                        <input type="text" id="imagePath" class="form-control" wire:model="imagePath">
                        @error('imagePath') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="imageCaption" class="form-label">Image Caption</label>
                        <input type="text" id="imageCaption" class="form-control" wire:model="imageCaption">
                        @error('imageCaption') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="activity" class="form-label">Activity</label>
                        <input type="text" id="activity" class="form-control" wire:model="activity">
                        @error('activity') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" class="form-control" wire:model="description"></textarea>
                        @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="isPrimary" class="form-label">Is Primary</label>
                        <select id="isPrimary" class="form-select" wire:model="isPrimary">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                        @error('isPrimary') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        @if($imagePath)
                            <label for="imagePreview" class="form-label">Image Preview</label>
                            <img src="{{ asset($imagePath) }}" alt="Image Preview" class="img-thumbnail" style="max-height: 150px;">
                        @endif
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-secondary" wire:click="resetInputFields">Cancel</button>
        </form>

        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Location Name</th>
                    <th>Image Preview</th>
                    <th>Caption</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($galleries as $gallery)
                    <tr>
                        <td>{{ $gallery->id }}</td>
                        <td>{{ $gallery->location_name }}</td>
                        <td>
                            <img src="{{ asset($gallery->image_path) }}" alt="Preview" class="img-thumbnail" style="max-height: 50px;">
                        </td>
                        <td>{{ $gallery->image_caption }}</td>
                        <td>
                            <button class="btn btn-sm btn-info" wire:click="edit({{ $gallery->id }})">Edit</button>
                            <button class="btn btn-sm btn-danger" wire:click="delete({{ $gallery->id }})">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

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
                            <li class="page-item {{ $galleries->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link b-r-left" wire:click="previousPage" href="#" aria-label="Previous">
                                    Previous
                                </a>
                            </li>
                            @php
                                $currentPage = $galleries->currentPage();
                                $lastPage = $galleries->lastPage();
                                $range = 2;
                                $start = max(1, $currentPage - $range);
                                $end = min($lastPage, $currentPage + $range);
                            @endphp
                            @for ($i = $start; $i <= $end; $i++)
                                <li class="page-item {{ $currentPage == $i ? 'active' : '' }}" aria-current="{{ $currentPage == $i ? 'page' : '' }}">
                                    <a class="page-link" wire:click="gotoPage({{ $i }})" href="#">{{ $i }}</a>
                                </li>
                            @endfor
                            <li class="page-item {{ $galleries->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link b-r-right" wire:click="nextPage" href="#" aria-label="Next">
                                    Next
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <span class="text-muted">
                        Zeigt {{ $galleries->firstItem() }} bis {{ $galleries->lastItem() }} von {{ $galleries->total() }} Einträgen
                    </span>
                </div>
            </div>
        </div>



    </div>
</div>
