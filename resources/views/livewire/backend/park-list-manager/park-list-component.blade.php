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
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                <h5 class="mb-0">Freizeitparks</h5>
                <div class="d-flex flex-wrap align-items-center gap-2 w-100 w-md-auto">
                    <div class="input-group flex-grow-1 flex-md-grow-0">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Suche nach Name oder Standort" wire:model.live.throttle.150ms="search">
                    </div>
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
                    <button wire:click="openCreateModal" class="btn btn-primary">
                        <i class="fas fa-plus f-s-16"></i> Neuer Park
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-vcenter responsive-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Logo</th>
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
                            <td data-label="#">{{ $parks instanceof \Illuminate\Pagination\LengthAwarePaginator ? $parks->firstItem() + $index : $index + 1 }}</td>
                            <td data-label="Logo">
                                @if($park->logo_url)
                                    <img src="{{ asset($park->logo_url) }}" alt="{{ $park->name }} Logo" style="max-width: 50px; max-height: 50px;" class="img-fluid">
                                @else
                                    <span class="text-secondary">–</span>
                                @endif
                            </td>
                            <td data-label="Name" class="breakable-column">{{ $park->name }}</td>
                            <td data-label="Land">{{ $park->country }}</td>
                            <td data-label="Latitude">{{ $park->latitude }}</td>
                            <td data-label="Longitude">{{ $park->longitude }}</td>
                            <td data-label="URL" class="breakable-column">
                                @if($park->url)
                                    <a href="{{ $park->url }}" target="_blank" class="text-secondary">{{ $park->url }}</a>
                                @else
                                    <span class="text-secondary">–</span>
                                @endif
                            </td>
                            <td data-label="Beschreibung" class="breakable-column">{{ $park->description ?? 'Keine Beschreibung' }}</td>
                            <td data-label="Geöffnet">
                                @if($park->open_today)
                                    <span class="badge bg-success">Ja</span>
                                @else
                                    <span class="badge bg-danger">Nein</span>
                                @endif
                            </td>
                            <td data-label="Aktionen" class="text-end">
                                <button wire:click="openEditModal({{ $park->id }})" class="btn btn-success icon-btn b-r-4">
                                    <i class="fas fa-edit f-s-16"></i>
                                </button>
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
                            <td colspan="10" class="text-center text-secondary">Keine Einträge gefunden</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            <div class="row align-items-center flex-column flex-md-row gap-3 gap-md-0">
                <div class="col-12 col-md-3">
                    <select wire:model.change="perPage" class="form-select form-select-sm w-100">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
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
                <div class="col-12 col-md-3 text-center text-md-end">
                    <span class="text-muted">
                        Zeigt {{ $parks->firstItem() }} bis {{ $parks->lastItem() }} von {{ $parks->total() }} Einträgen
                    </span>
                </div>
            </div>
        </div>


    </div>

    <!-- Modal für die Bearbeitung -->
    <div class="modal fade" id="editParkModal" tabindex="-1" aria-modal="true" role="dialog" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary-800">
                    <h1 class="modal-title fs-5 text-white">{{ $parkIdToEdit ? 'Park bearbeiten' : 'Neuer Park' }}</h1>
                    <button type="button" class="fs-5 border-0 bg-none text-white" data-bs-dismiss="modal"><i class="fa-solid fa-xmark fs-3"></i></button>
                </div>
                <div class="modal-body">
                    <livewire:backend.park-list-manager.park-form-component :id="$parkIdToEdit" wire:key="form-{{ $parkIdToEdit ?? 'create' }}" />
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

            Livewire.on('open-modal', () => {
                const modal = new bootstrap.Modal(document.getElementById('editParkModal'));
                modal.show();
            });

            Livewire.on('close-modal', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('editParkModal'));
                if (modal) {
                    modal.hide();
                }
            });
        });
    </script>
@endpush
@push('styles')
    <style>
        /* Sicherstellen, dass Text in der Tabelle auswählbar ist */
        .responsive-table,
        .responsive-table th,
        .responsive-table td,
        .responsive-table .breakable-column,
        .responsive-table a,
        .responsive-table span {
            user-select: auto !important; /* Text auswählbar machen */
            -webkit-user-select: auto !important; /* Für Safari */
            -moz-user-select: auto !important; /* Für Firefox */
            -ms-user-select: auto !important; /* Für Edge */
        }

        /* Falls ein übergeordnetes Element das Auswählen verhindert */
        .card,
        .card-body {
            user-select: auto !important;
            -webkit-user-select: auto !important;
            -moz-user-select: auto !important;
            -ms-user-select: auto !important;
        }

        /* Allgemeine Stile für die Tabelle (bestehendes CSS) */
        .responsive-table {
            width: 100%;
            border-collapse: collapse;
        }

        .responsive-table th,
        .responsive-table td {
            padding: 8px;
            text-align: left;
        }

        /* Standardverhalten für breakable-column auf Desktop */
        .breakable-column {
            word-wrap: break-word;
            white-space: normal;
            max-width: 200px;
        }

        /* Card-Header Stile */
        .card-header {
            padding: 1rem 1.5rem;
        }

        .card-header h5 {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .card-header .form-control,
        .card-header .form-select {
            min-width: 150px;
            max-width: 200px;
        }

        .card-header .input-group {
            max-width: 250px;
        }

        .card-header .btn {
            white-space: nowrap;
        }

        /* Card-Footer Stile */
        .card-footer .pagination {
            flex-wrap: wrap;
        }

        .card-footer .pagination .page-link {
            padding: 6px 12px;
        }

        /* Desktop (≥ 1025px) */
        @media (min-width: 1025px) {
            .card-header .d-flex.flex-wrap {
                flex-wrap: nowrap;
            }

            .card-header .form-control,
            .card-header .form-select,
            .card-header .btn {
                margin-left: 10px;
            }

            .card-header .input-group {
                margin-left: 0;
            }
        }

        /* Tablets (769px bis 1024px) */
        @media (min-width: 769px) and (max-width: 1024px) {
            /* Tabelle: Kartenansicht */
            .responsive-table thead {
                display: none;
            }

            .responsive-table tr {
                display: block;
                margin-bottom: 20px;
                padding: 12px;
                border: 1px solid #ddd;
                border-radius: 8px;
                background-color: #fff;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                max-width: 100%;
            }

            .responsive-table td {
                display: flex;
                align-items: center;
                text-align: left;
                padding: 8px;
                border-bottom: 1px solid #eee;
                font-size: 1rem;
            }

            .responsive-table td::before {
                content: attr(data-label);
                flex: 0 0 25%;
                font-weight: bold;
                color: #555;
                margin-right: 12px;
            }

            .breakable-column {
                flex-direction: column;
                align-items: flex-start;
            }

            .breakable-column::before {
                display: block;
                flex: none;
                margin-bottom: 6px;
            }

            .breakable-column a,
            .breakable-column span {
                word-wrap: break-word;
                white-space: normal;
                max-width: 100%;
                font-size: 1rem;
            }

            .responsive-table td[data-label="Logo"] {
                justify-content: center;
            }

            .responsive-table td[data-label="Logo"] img,
            .responsive-table td[data-label="Logo"] span {
                margin: 0 auto;
            }

            .responsive-table td[data-label="Aktionen"] {
                justify-content: flex-end;
                border-bottom: none;
            }

            .responsive-table td:last-child {
                border-bottom: none;
            }

            /* Card-Header */
            .card-header .d-flex.flex-wrap {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 10px;
            }

            .card-header .form-control,
            .card-header .form-select,
            .card-header .btn,
            .card-header .input-group {
                width: auto;
                min-width: 140px;
                max-width: 180px;
                margin-left: 0;
            }

            .card-header .input-group {
                max-width: 220px;
            }

            /* Card-Footer */
            .card-footer .row {
                flex-direction: row;
                gap: 0;
            }

            .card-footer .col-12 {
                margin-bottom: 0;
            }

            .card-footer .text-md-end {
                text-align: right !important;
            }
        }

        /* Querformat (576px bis 768px) */
        @media (min-width: 576px) and (max-width: 768px) {
            /* Tabelle */
            .responsive-table thead {
                display: none;
            }

            .responsive-table tr {
                display: block;
                margin-bottom: 15px;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 8px;
                background-color: #fff;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            .responsive-table td {
                display: flex;
                align-items: center;
                text-align: left;
                padding: 6px;
                border-bottom: 1px solid #eee;
                font-size: 0.9rem;
            }

            .responsive-table td::before {
                content: attr(data-label);
                flex: 0 0 35%;
                font-weight: bold;
                color: #555;
                margin-right: 8px;
            }

            .breakable-column {
                flex-direction: column;
                align-items: flex-start;
            }

            .breakable-column::before {
                display: block;
                flex: none;
                margin-bottom: 4px;
            }

            .breakable-column a,
            .breakable-column span {
                word-wrap: break-word;
                white-space: normal;
                max-width: 100%;
            }

            .responsive-table td[data-label="Logo"] {
                justify-content: center;
            }

            .responsive-table td[data-label="Logo"] img,
            .responsive-table td[data-label="Logo"] span {
                margin: 0 auto;
            }

            .responsive-table td[data-label="Aktionen"] {
                justify-content: flex-end;
                border-bottom: none;
            }

            .responsive-table td:last-child {
                border-bottom: none;
            }

            /* Card-Header */
            .card-header .d-flex.flex-wrap {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 8px;
            }

            .card-header .form-control,
            .card-header .form-select,
            .card-header .btn,
            .card-header .input-group {
                width: auto;
                min-width: 120px;
                max-width: 150px;
                margin-left: 0;
            }

            .card-header .input-group {
                max-width: 200px;
            }

            /* Card-Footer */
            .card-footer .row {
                flex-direction: row;
                gap: 0;
            }

            .card-footer .col-12 {
                margin-bottom: 0;
            }

            .card-footer .text-md-end {
                text-align: right !important;
            }
        }

        /* Hochformat (≤ 576px) */
        @media (max-width: 576px) {
            /* Tabelle */
            .responsive-table thead {
                display: none;
            }

            .responsive-table tr {
                display: block;
                margin-bottom: 20px;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 8px;
                background-color: #fff;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            .responsive-table td {
                display: flex;
                align-items: center;
                text-align: left;
                padding: 8px;
                border-bottom: 1px solid #eee;
            }

            .responsive-table td::before {
                content: attr(data-label);
                flex: 0 0 40%;
                font-weight: bold;
                color: #555;
                margin-right: 10px;
            }

            .breakable-column {
                flex-direction: column;
                align-items: flex-start;
            }

            .breakable-column::before {
                display: block;
                flex: none;
                margin-bottom: 5px;
            }

            .breakable-column a,
            .breakable-column span {
                word-wrap: break-word;
                white-space: normal;
                max-width: 100%;
            }

            .responsive-table td[data-label="Logo"] {
                justify-content: center;
            }

            .responsive-table td[data-label="Logo"] img,
            .responsive-table td[data-label="Logo"] span {
                margin: 0 auto;
            }

            .responsive-table td[data-label="Aktionen"] {
                justify-content: flex-end;
                border-bottom: none;
            }

            .responsive-table td:last-child {
                border-bottom: none;
            }

            /* Card-Header */
            .card-header .d-flex.flex-wrap {
                flex-direction: column;
                gap: 10px;
            }

            .card-header .form-control,
            .card-header .form-select,
            .card-header .btn,
            .card-header .input-group {
                width: 100%;
                max-width: none;
                min-width: unset;
                margin-left: 0;
            }

            /* Card-Footer */
            .card-footer .row {
                text-align: center;
            }

            .card-footer .col-12 {
                margin-bottom: 10px;
            }

            .card-footer .text-md-end {
                text-align: center !important;
            }
        }
    </style>
@endpush
