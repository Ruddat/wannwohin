<div>
    <form wire:submit.prevent="subscribe">
        <div class="mb-3">
            <input
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="Deine Email"
                wire:model="email" />
            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <button type="submit" class="btn btn-warning w-100">Abonnieren</button>
    </form>

</div>
