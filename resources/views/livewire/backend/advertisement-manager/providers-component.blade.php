<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}




    <div class="container">
        <h1 class="mb-4">Anbieter verwalten</h1>

        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <h3>{{ $isEditing ? 'Anbieter bearbeiten' : 'Anbieter erstellen' }}</h3>
                <form wire:submit.prevent="save">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" class="form-control" wire:model="name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-Mail</label>
                        <input type="email" id="email" class="form-control" wire:model="email">
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefon</label>
                        <input type="text" id="phone" class="form-control" wire:model="phone">
                        @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="website" class="form-label">Website</label>
                        <input type="url" id="website" class="form-control" wire:model="website">
                        @error('website') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Beschreibung</label>
                        <textarea id="description" class="form-control" wire:model="description"></textarea>
                        @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="contact_person" class="form-label">Ansprechpartner</label>
                        <input type="text" id="contact_person" class="form-control" wire:model="contact_person">
                        @error('contact_person') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="btn btn-success">{{ $isEditing ? 'Aktualisieren' : 'Speichern' }}</button>
                    <button type="button" class="btn btn-secondary" wire:click="resetFields">Abbrechen</button>
                </form>
            </div>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>E-Mail</th>
                    <th>Telefon</th>
                    <th>Website</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($providers as $provider)
                    <tr>
                        <td>{{ $provider->id }}</td>
                        <td>{{ $provider->name }}</td>
                        <td>{{ $provider->email ?? 'N/A' }}</td>
                        <td>{{ $provider->phone ?? 'N/A' }}</td>
                        <td>{{ $provider->website ?? 'N/A' }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" wire:click="edit({{ $provider->id }})">Bearbeiten</button>
                            <button class="btn btn-sm btn-danger" wire:click="delete({{ $provider->id }})">Löschen</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Keine Anbieter gefunden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $providers->links() }}
    </div>















</div>
