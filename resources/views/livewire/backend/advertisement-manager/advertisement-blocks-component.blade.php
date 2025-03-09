<div class="container">
    <h1 class="mb-4">Werbeblöcke verwalten</h1>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <button class="btn btn-primary mb-3" wire:click="resetFields">Neuen Werbeblock erstellen</button>

    <div class="card mb-4">
        <div class="card-body">
            <h3>{{ $isEditing ? 'Werbeblock bearbeiten' : 'Werbeblock erstellen' }}</h3>
            <form wire:submit.prevent="save">
                <div class="mb-3">
                    <label for="title" class="form-label">Titel</label>
                    <input type="text" id="title" class="form-control" wire:model="title">
                    @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">Typ</label>
                    <select id="type" class="form-select" wire:model="type">
                        <option value="banner">Banner (Bild/Link)</option>
                        <option value="widget">Widget (HTML + JS)</option>
                        <option value="script">Skript (nur JS)</option>
                    </select>
                    @error('type') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                    <textarea id="code" class="form-control" rows="6" wire:model="code"></textarea>
                    <small class="form-text text-muted">
                        Füge den gesamten Code hier ein (HTML, CSS, JS). Für Banner: HTML inkl. Link (z. B. &lt;a href="..."&gt;).
                    </small>
                    @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="position" class="form-label">Position</label>
                    <select id="position" class="form-select" wire:model="position">
                        @foreach($availablePositions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('position') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="providerId" class="form-label">Anbieter</label>
                    <select id="providerId" class="form-select" wire:model="providerId">
                        <option value="">Wähle einen Anbieter</option>
                        @foreach ($providers as $provider)
                            <option value="{{ $provider->id }}">{{ $provider->name }} ({{ $provider->email ?? 'Kein E-Mail' }})</option>
                        @endforeach
                    </select>
                    @error('providerId') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                @if($code)
                    <div class="mb-3">
                        <label class="form-label">Vorschau</label>
                        <div class="border p-3 bg-light">{!! $code !!}</div>
                    </div>
                @endif

                <button type="submit" class="btn btn-success">{{ $isEditing ? 'Aktualisieren' : 'Speichern' }}</button>
                <button type="button" class="btn btn-secondary" wire:click="resetFields">Abbrechen</button>
            </form>
        </div>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Titel</th>
                <th>Typ</th>
                <th>Inhalt</th>
                <th>Position</th>
                <th>Anbieter</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($advertisements as $advertisement)
                <tr>
                    <td>{{ $advertisement->id }}</td>
                    <td>{{ $advertisement->title }}</td>
                    <td>{{ ucfirst($advertisement->type) }}</td>
                    <td>{{ Str::limit($advertisement->script, 50) }}</td>
                    <td>{{ $advertisement->position ? $availablePositions[$advertisement->position] : 'Keine' }}</td>
                    <td>{{ $advertisement->provider->name ?? 'N/A' }}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" wire:click="edit({{ $advertisement->id }})">Bearbeiten</button>
                        <button class="btn btn-sm btn-danger" wire:click="delete({{ $advertisement->id }})">Löschen</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Keine Werbeblöcke gefunden.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $advertisements->links() }}
</div>
