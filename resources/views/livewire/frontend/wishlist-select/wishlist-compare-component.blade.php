<div>
    @if(count($locations) > 0)
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Standorte vergleichen</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Bild</th>
                                <th>Name</th>
                                <th>Adresse</th>
                                <th>Bewertung</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($locations as $location)
                                <tr>
                                    <td>
                                        @php
                                            // Erstes Bild aus primaryImage oder Galerie abrufen
                                            $imagePath = $location->primaryImage() ?: optional($location->images()->first())->path;
                                        @endphp

                                        @if($imagePath)
                                            <img src="{{ asset($imagePath) }}" alt="{{ $location->title }}" class="img-thumbnail" style="width: 100px;">
                                        @else
                                            <span class="text-muted">Kein Bild</span>
                                        @endif
                                    </td>
                                    <td>{{ $location->title }}</td>
                                    <td>{{ $location->address ?? 'Keine Adresse' }}</td>
                                    <td>
                                        ⭐ {{ $location->rating ?? 'Keine Bewertung' }}
                                    </td>
                                    <td>
                                        <button wire:click="removeFromCompare({{ $location->id }})" class="btn btn-danger btn-sm">
                                            Entfernen
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button wire:click="clearCompare" class="btn btn-warning mt-3">
                    Vergleichsliste leeren
                </button>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <p>Es wurden noch keine Standorte zum Vergleich hinzugefügt.</p>
        </div>
    @endif
</div>
