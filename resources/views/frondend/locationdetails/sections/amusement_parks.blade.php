@if ($parks_with_opening_times->isNotEmpty())
<section id="freizeitparks" class="section section-no-border bg-color-secondary m-0 py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="text-color-dark font-weight-extra-bold">Freizeitparks in der Nähe von {{ $location->title }}</h2>
            </div>
        </div>

        <div class="row">
            @foreach ($parks_with_opening_times as $item)
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title text-primary">{{ $item['park']->name }}</h5>
                            <p class="card-text">
                                <strong>Ort:</strong> {{ $item['park']->location ?? 'Unbekannt' }}<br>
                                <strong>Entfernung:</strong> {{ round($item['park']->distance, 1) }} km
                            </p>
                            @if ($item['opening_times'])
                                <p>
                                    <strong>Heute geöffnet:</strong> {{ $item['opening_times']['opened_today'] ? 'Ja' : 'Nein' }}<br>
                                    <strong>Öffnet um:</strong> {{ $item['opening_times']['open_from'] ?? 'N/A' }}<br>
                                    <strong>Schließt um:</strong> {{ $item['opening_times']['closed_from'] ?? 'N/A' }}
                                </p>
                            @else
                                <p class="text-danger">Keine Öffnungszeiten verfügbar.</p>
                            @endif

                            <!-- Button zum Öffnen des Modals für Wartezeiten -->
                            @if (!empty($item['waiting_times']))
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#waitingTimesModal{{ $item['park']->id }}">
                                    Wartezeiten anzeigen
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

<!-- Modal für Wartezeiten -->
<div class="modal fade" id="waitingTimesModal{{ $item['park']->id }}" tabindex="-1" aria-labelledby="waitingTimesLabel{{ $item['park']->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="waitingTimesLabel{{ $item['park']->id }}">Wartezeiten für {{ $item['park']->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>
            <div class="modal-body">
                @if (!empty($item['waiting_times']))
                    <table class="table table-striped text-center">
                        <thead>
                            <tr>
                                <th>Attraktion</th>
                                <th style="width: 150px;">Wartezeit (Min)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($item['waiting_times'] as $wait)
                                @php
                                    // Wartezeit-Klassifikation
                                    $statusColor = match (true) {
                                        $wait['waitingtime'] <= 10 => 'bg-success text-white',  // Grün
                                        $wait['waitingtime'] <= 30 => 'bg-warning text-dark',  // Gelb
                                        $wait['waitingtime'] > 30 => 'bg-danger text-white',   // Rot
                                        default => 'bg-secondary text-white',                 // Grau für keine Daten
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $wait['name'] }}</td>
                                    <td class="{{ $statusColor }}">
                                        {{ $wait['waitingtime'] ?? 'N/A' }}
                                    </td>
                                    <td>{{ $wait['status'] === 'opened' ? 'Geöffnet' : 'Geschlossen' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-danger">Keine Wartezeiten verfügbar.</p>
                @endif
            </div>
        </div>
    </div>
</div>



            @endforeach
        </div>
    </div>
</section>
@endif

<style>
    .status-indicator {
    display: inline-block;
    width: 20px;
    height: 20px;
    border-radius: 3px;
}
.bg-success {
    background-color: #28a745;
}
.bg-warning {
    background-color: #ffc107;
}
.bg-danger {
    background-color: #dc3545;
}
.bg-secondary {
    background-color: #6c757d;
}

</style>
