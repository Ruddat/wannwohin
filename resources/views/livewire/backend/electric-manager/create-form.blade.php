<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Add New Electric Standard</h3>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="store">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="country_name" class="form-label">Country Name</label>
                    <input type="text" id="country_name" class="form-control" wire:model.defer="country_name">
                    @error('country_name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="country_code" class="form-label">Country Code</label>
                    <input type="text" id="country_code" class="form-control" wire:model.defer="country_code">
                    @error('country_code') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="power" class="form-label">Power</label>
                    <input type="text" id="power" class="form-control" wire:model.defer="power">
                    @error('power') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="info" class="form-label">Additional Info</label>
                    <textarea id="info" class="form-control" rows="3" wire:model.defer="info"></textarea>
                    @error('info') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Plug Types</label>
                <div class="row">
                    @foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N'] as $type)
                        <div class="col-md-2 col-sm-3 col-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="typ_{{ strtolower($type) }}" wire:model.defer="typ_{{ strtolower($type) }}">
                                <label class="form-check-label" for="typ_{{ strtolower($type) }}">Type {{ $type }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('plug_types') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" wire:click="resetInputFields" class="btn btn-secondary ms-2">Cancel</button>
            </div>
        </form>
    </div>
</div>
