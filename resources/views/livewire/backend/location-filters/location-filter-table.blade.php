<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Standort-Filter verwalten</h5>
    </div>

    <div class="card-body">
        <!-- Suchfeld -->
        <div class="mb-3">
            <input type="text" class="form-control" placeholder="Suche nach Standort, Typ oder Überschrift..."
                   wire:model.live="search">
        </div>

        <!-- Tabelle -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Standort</th>
                        <th>Kategorie</th>
                        <th>Überschrift</th>
                        <th>Text</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($filters as $filter)
                        <tr>
                            <td>{{ $filter->id }}</td>
                            <td>{{ $filter->location->title ?? 'Unbekannt' }}</td>
                            <td>{{ $filter->text_type }}</td>
                            <td>{{ $filter->uschrift }}</td>
                            <td>{{ Str::limit($filter->text, 50) }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm" wire:click="delete({{ $filter->id }})">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Keine Einträge gefunden.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $filters->links() }}
        </div>
    </div>
</div>
