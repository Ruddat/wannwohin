<div class="container-xl mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">Statische Seiten</h3>
                    <div class="input-group w-auto">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" wire:model.debounce.300ms="search" class="form-control" placeholder="Suche..." style="max-width: 300px;">
                    </div>
                </div>
                <div class="card-body p-0">
                    @if (session()->has('message'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($editId)
                        <div class="p-3">
                            <form wire:submit.prevent="update">
                                <div class="mb-3">
                                    <label class="form-label">Titel</label>
                                    <input type="text" class="form-control" wire:model="title">
                                    @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Inhalt</label>
                                    <livewire:jodit-text-editor
                                        wire:model.live="body"
                                        :buttons="['bold', 'italic', 'underline', '|', 'font', 'fontsize', '|', 'link', 'image']"
                                        tabindex="2"
                                    />
                                    @error('body') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Speichern</button>
                                    <button type="button" class="btn btn-secondary" wire:click="cancelEdit">Abbrechen</button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="w-1 cursor-pointer" wire:click="sortBy('slug')">
                                            Slug
                                            @if($sortField === 'slug')
                                                <span class="text-muted">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </th>
                                        <th class="cursor-pointer" wire:click="sortBy('title')">
                                            Titel
                                            @if($sortField === 'title')
                                                <span class="text-muted">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </th>
                                        <th>Inhalt (Vorschau)</th>
                                        <th class="w-1">Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($staticPages as $page)
                                        <tr>
                                            <td>{{ $page->slug }}</td>
                                            <td>{{ $page->title }}</td>
                                            <td class="text-truncate" style="max-width: 300px;" title="{{ strip_tags($page->body) }}">
                                                {{ Str::limit(strip_tags($page->body), 50) }}
                                            </td>
                                            <td>
                                                <button wire:click="edit('{{ $page->slug }}')" class="btn btn-outline-primary btn-sm">
                                                    <i class="ti ti-edit"></i> Bearbeiten
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Keine Seiten gefunden.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div>{{ $staticPages->links() }}</div>
                    <div class="d-flex align-items-center">
                        <label for="perPageSelect" class="me-2 mb-0">Einträge pro Seite:</label>
                        <select wire:model="perPage" id="perPageSelect" class="form-select form-select-sm">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@assets
<!-- Include Jodit CSS Styling -->
<link rel="stylesheet" href="//unpkg.com/jodit@4.1.16/es2021/jodit.min.css">

<!-- Include the Jodit JS Library -->
<script src="//unpkg.com/jodit@4.1.16/es2021/jodit.min.js"></script>
@endassets
