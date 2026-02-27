<div class="container py-4">

    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header fw-bold">Profil bearbeiten</div>
        <div class="card-body">
            <form wire:submit.prevent="updateProfile">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" wire:model.defer="name" class="form-control">
                    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">E-Mail</label>
                    <input type="email" wire:model.defer="email" class="form-control">
                    @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <button class="btn btn-primary">Speichern</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header fw-bold">Passwort ändern</div>
        <div class="card-body">
            <form wire:submit.prevent="updatePassword">
                <div class="mb-3">
                    <label class="form-label">Aktuelles Passwort</label>
                    <input type="password" wire:model.defer="current_password" class="form-control">
                    @error('current_password') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Neues Passwort</label>
                    <input type="password" wire:model.defer="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Passwort bestätigen</label>
                    <input type="password" wire:model.defer="password_confirmation" class="form-control">
                    @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <button class="btn btn-warning">Passwort ändern</button>
            </form>
        </div>
    </div>
</div>
