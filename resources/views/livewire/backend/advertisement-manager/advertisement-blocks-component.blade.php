<div>
    <div class="container">
        <h1 class="mb-4">Werbeblöcke verwalten</h1>

        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        <button class="btn btn-primary mb-3" wire:click="resetFields">Neuen Werbeblock erstellen</button>

        @if ($isEditing)
            @include('livewire.backend.advertisement-manager.partials.edit-advertisement')
        @else
            @include('livewire.backend.advertisement-manager.partials.create-advertisement')
        @endif

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Titel</th>
                    <th>Inhalt</th>
                    <th>Link</th>
                    <th>Anbieter</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($advertisements as $advertisement)
                    <tr>
                        <td>{{ $advertisement->id }}</td>
                        <td>{{ $advertisement->title }}</td>
                        <td>{{ Str::limit($advertisement->content, 50) }}</td>
                        <td><a href="{{ $advertisement->link }}" target="_blank">{{ $advertisement->link }}</a></td>
                        <td>{{ $advertisement->provider->name ?? 'N/A' }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" wire:click="edit({{ $advertisement->id }})">Bearbeiten</button>
                            <button class="btn btn-sm btn-danger" wire:click="delete({{ $advertisement->id }})">Löschen</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Keine Werbeblöcke gefunden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $advertisements->links() }}
    </div>
</div>
