<div class="container-xl mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Freizeitparks</h3>
            <div class="d-flex align-items-center gap-2">
                <input type="text" class="form-control" placeholder="Suche nach Name oder Standort" wire:model.live.throttle.150ms="search">
                <select class="form-select" wire:model.change="country">
                    <option value="">Alle Länder</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country }}">{{ $country }}</option>
                    @endforeach
                </select>
                <select class="form-select" wire:model.change="sortBy">
                    <option value="created_at">Neueste zuerst</option>
                    <option value="name">Name (A-Z)</option>
                    <option value="country">Land</option>
                </select>
                <select class="form-select" wire:model.change="sortDirection">
                    <option value="asc">Aufsteigend</option>
                    <option value="desc">Absteigend</option>
                </select>
                <select class="form-select" wire:model.change="perPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">Alle</option>
                </select>
                <a href="{{ route('verwaltung.site-manager.park-manager.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    Neuer Park
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Land</th>
                        <th>Latitude</th>
                        <th>Longitude</th>
                        <th>URL</th>
                        <th>Beschreibung</th>
                        <th>Geöffnet</th>
                        <th class="text-end">Aktionen</th>
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
                                @if($park->url)
                                    <a href="{{ $park->url }}" target="_blank" class="text-muted">{{ Str::limit($park->url, 20) }}</a>
                                @else
                                    <span class="text-muted">–</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($park->description ?? 'Keine Beschreibung', 50) }}</td>
                            <td>
                                @if($park->open_today)
                                    <span class="badge bg-success">Ja</span>
                                @else
                                    <span class="badge bg-danger">Nein</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-list flex-nowrap">
                                    <a href="{{ route('verwaltung.site-manager.park-manager.edit', $park->id) }}" class="btn btn-sm btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                            <path d="M16 5l3 3" />
                                        </svg>
                                        Bearbeiten
                                    </a>
                                    <button wire:click="updateCoordinates({{ $park->id }})" class="btn btn-sm btn-outline-secondary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <circle cx="12" cy="11" r="3" />
                                            <path d="M17.657 16.657a8 8 0 1 0 -11.314 0l5.657 5.657l5.657 -5.657z" />
                                        </svg>
                                        Koordinaten
                                    </button>
                                    <button wire:click="confirmDelete({{ $park->id }})" class="btn btn-sm btn-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 7l16 0" />
                                            <path d="M10 11l0 6" />
                                            <path d="M14 11l0 6" />
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                        </svg>
                                        Löschen
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Keine Einträge gefunden</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted">
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

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('show-toast', ({ type, message }) => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type,
                    title: message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'swal2-tabler-toast'
                    }
                });
            });

            Livewire.on('delete-prompt', (data) => {
                Swal.fire({
                    title: data.title || 'Sind Sie sicher?',
                    text: data.text || 'Dieser Vorgang kann nicht rückgängig gemacht werden.',
                    icon: data.icon || 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#206bc4', // Tabler Primary
                    cancelButtonColor: '#d63939', // Tabler Danger
                    confirmButtonText: data.confirmButtonText || 'Ja, löschen!',
                    cancelButtonText: data.cancelButtonText || 'Abbrechen',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('goOn-Delete');
                    }
                });
            });
        });
    </script>
@endpush
