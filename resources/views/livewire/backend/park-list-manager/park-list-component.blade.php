<div class="container-fluid">
    <!-- Breadcrumb start -->
    <div class="row m-1">
        <div class="col-12">
            <h4 class="main-title">Freizeitparks</h4>
            <ul class="app-line-breadcrumbs mb-3">
                <li>
                    <a href="#" class="f-s-14 f-w-500">
                        <span>
                            <i class="fas fa-folder f-s-16"></i> Verwaltung
                        </span>
                    </a>
                </li>
                <li class="active">
                    <a href="#" class="f-s-14 f-w-500">Freizeitparks</a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Breadcrumb end -->

    <div class="card">
        <div class="card-header">
            <h5>Freizeitparks</h5>
            <div class="float-end d-flex align-items-center gap-2">
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
                    <i class="fas fa-plus f-s-16"></i> Neuer Park
                </a>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-vcenter">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Logo</th> <!-- Neue Spalte für das Logo -->
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
                            <td>
                                @if($park->logo_url)
                                    <img src="{{ asset($park->logo_url) }}" alt="{{ $park->name }} Logo" style="max-width: 50px; max-height: 50px;" class="img-fluid">
                                @else
                                    <span class="text-secondary">–</span>
                                @endif
                            </td> <!-- Logo-Anzeige -->
                            <td>{{ $park->name }}</td>
                            <td>{{ $park->country }}</td>
                            <td>{{ $park->latitude }}</td>
                            <td>{{ $park->longitude }}</td>
                            <td>
                                @if($park->url)
                                    <a href="{{ $park->url }}" target="_blank" class="text-secondary">{{ Str::limit($park->url, 20) }}</a>
                                @else
                                    <span class="text-secondary">–</span>
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
                                <a href="{{ route('verwaltung.site-manager.park-manager.edit', $park->id) }}" class="btn btn-success icon-btn b-r-4">
                                    <i class="fas fa-edit f-s-16"></i>
                                </a>
                                <button wire:click="updateCoordinates({{ $park->id }})" class="btn btn-outline-secondary icon-btn b-r-4">
                                    <i class="fas fa-map-marker-alt f-s-16"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $park->id }})" class="btn btn-danger icon-btn b-r-4">
                                    <i class="fas fa-trash f-s-16"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-secondary">Keine Einträge gefunden</td> <!-- colspan angepasst auf 10 -->
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

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
                            <li class="page-item {{ $parks->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link b-r-left" wire:click="previousPage" href="#" aria-label="Previous">
                                    Previous
                                </a>
                            </li>
                            @php
                                $currentPage = $parks->currentPage();
                                $lastPage = $parks->lastPage();
                                $range = 2;
                                $start = max(1, $currentPage - $range);
                                $end = min($lastPage, $currentPage + $range);
                            @endphp
                            @for ($i = $start; $i <= $end; $i++)
                                <li class="page-item {{ $currentPage == $i ? 'active' : '' }}" aria-current="{{ $currentPage == $i ? 'page' : '' }}">
                                    <a class="page-link" wire:click="gotoPage({{ $i }})" href="#">{{ $i }}</a>
                                </li>
                            @endfor
                            <li class="page-item {{ $parks->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link b-r-right" wire:click="nextPage" href="#" aria-label="Next">
                                    Next
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <span class="text-muted">
                        Zeigt {{ $parks->firstItem() }} bis {{ $parks->lastItem() }} von {{ $parks->total() }} Einträgen
                    </span>
                </div>
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
                    confirmButtonColor: '#206bc4',
                    cancelButtonColor: '#d63939',
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
