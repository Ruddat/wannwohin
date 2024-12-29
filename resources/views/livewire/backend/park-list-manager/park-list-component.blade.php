<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Freizeitparks</h3>
            <div class="d-flex align-items-center">
                <input type="text" class="form-control me-2" placeholder="Suche nach Name oder Standort" wire:model.live.throttle.150ms="search">
                <select class="form-select me-2" wire:model.change="country">
                    <option value="">Alle Länder</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country }}">{{ $country }}</option>
                    @endforeach
                </select>
                <select class="form-select me-2" wire:model.change="sortBy">
                    <option value="created_at">Neueste zuerst</option>
                    <option value="name">Name (A-Z)</option>
                    <option value="country">Land</option>
                </select>
                <select class="form-select me-2" wire:model.change="sortDirection">
                    <option value="asc">Aufsteigend</option>
                    <option value="desc">Absteigend</option>
                </select>
                <select class="form-select me-2" wire:model.change="perPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">Alle</option>
                </select>
                <a href="{{ route('park-manager.create') }}" class="btn btn-success">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    Neuer Park
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table card-table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Land</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>Geöffnet</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($parks as $index => $park)
                        <tr>
                            <td>{{ $parks instanceof \Illuminate\Pagination\LengthAwarePaginator ? $parks->firstItem() + $index : $index + 1 }}</td>
                            <td>{{ $park->name }}</td>
                            <td>{{ $park->country }}</td>
                            <td>{{ $park->latitude }}</td>
                            <td>{{ $park->longitude }}</td>
                            <td>
                                @if($park->open_today)
                                    <span class="badge bg-success">Ja</span>
                                @else
                                    <span class="badge bg-danger">Nein</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('park-manager.edit', $park->id) }}" class="btn btn-sm btn-primary">
                                    Bearbeiten
                                </a>
                                <button wire:click="updateCoordinates({{ $park->id }})" class="btn btn-sm btn-secondary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-map-pin">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <circle cx="12" cy="11" r="3" />
                                        <path d="M17.657 16.657a8 8 0 1 0 -11.314 0l5.657 5.657l5.657 -5.657z" />
                                    </svg>
                                    Koordinaten aktualisieren
                                </button>
                                <button wire:click="confirmDelete({{ $park->id }})" class="btn btn-sm btn-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-trash">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                    </svg>
                                    Löschen
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Keine Einträge gefunden</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div>
                @if($parks instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    Zeige {{ $parks->firstItem() }} bis {{ $parks->lastItem() }} von {{ $parks->total() }} Einträgen
                @else
                    Zeige alle {{ $parks->count() }} Einträge
                @endif
            </div>
            <div>
                {{ $parks instanceof \Illuminate\Pagination\LengthAwarePaginator ? $parks->links() : '' }}
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('livewire:initialized', () => {
    @this.on('swal', (data) => {
        Swal.fire({
            icon: data.icon || 'info',
            title: data.title || 'Notice',
            text: data.text || 'No message provided.',
            showConfirmButton: data.showConfirmButton ?? true,
        });
    });

    @this.on('delete-prompt', (data) => {
        Swal.fire({
            title: data.title || 'Are you sure?',
            text: data.text || 'This action is irreversible.',
            icon: data.icon || 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: data.confirmButtonText || 'Yes, delete it!',
            cancelButtonText: data.cancelButtonText || 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                @this.dispatch('goOn-Delete');
            }
        });
    });
});
</script>
