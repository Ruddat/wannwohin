<div class="favorite-activities">
    <h3>Meine Favoriten</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (empty($favorites))
        <p>Keine Favoriten hinzugefügt.</p>
    @else
    <ul class="list-group">
        @foreach ($favorites as $activity)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $activity['title'] ?? 'Unbekannt' }}</strong>
                    <span class="badge bg-secondary">{{ $activity['category'] ?? 'N/A' }}</span>
                    <small>{{ $activity['distance'] ?? 'N/A' }} km</small>
                </div>
                <button wire:click="removeFromFavorites('{{ $activity['id'] }}')"
                        class="btn btn-danger btn-sm">
                    Entfernen
                </button>
            </li>
        @endforeach
    </ul>
        <button wire:click="clearFavorites" class="btn btn-warning mt-3">
            Alle löschen
        </button>
    @endif
</div>
