@if ($parks_with_opening_times->isNotEmpty())
<section id="freizeitparks" class="section section-no-border bg-gradient-to-r from-gray-100 to-gray-200 m-0 py-5">
    <div class="container px-3 px-md-5">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="text-color-dark font-weight-extra-bold text-5xl mb-2 animate__animated animate__fadeIn">
                    Freizeitparks nahe {{ $location->title }}
                </h2>
                <div class="divider w-20 mx-auto bg-primary h-1 rounded"></div>
            </div>
        </div>

        <div class="row g-4">
            @foreach ($parks_with_opening_times as $item)
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card shadow-lg border-0 h-100 transform hover:-translate-y-2 transition-all duration-300">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title text-primary font-weight-bold mb-3 animate__animated animate__fadeInUp">
                                {{ $item['park']->name }}
                            </h5>
                            <div class="info-grid mb-3">
                                <div class="info-item">
                                    <span class="text-muted">Ort:</span>
                                    <span>{{ $item['park']->country ?? 'Unbekannt' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="text-muted">Entfernung:</span>
                                    <span>{{ round($item['park']->distance, 1) }} km</span>
                                </div>
                            </div>

                            @if ($item['opening_times'])
                                <div class="opening-times bg-gray-50 p-3 rounded-lg mb-3">
                                    <div class="status-badge {{ $item['opening_times']['opened_today'] ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item['opening_times']['opened_today'] ? 'Geöffnet' : 'Geschlossen' }}
                                    </div>
                                    <p class="mt-2 mb-0">
                                        <span class="text-muted">Öffnet:</span> {{ $item['opening_times']['open_from'] ?? 'N/A' }}<br>
                                        <span class="text-muted">Schließt:</span> {{ $item['opening_times']['closed_from'] ?? 'N/A' }}
                                    </p>
                                </div>
                            @else
                                <p class="text-danger text-sm">Keine Öffnungszeiten verfügbar</p>
                            @endif

                            @if (!empty($item['waiting_times']))
                                <button class="btn btn-primary btn-gradient w-100 mt-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#waitingTimesModal{{ $item['park']->id }}">
                                    Wartezeiten
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Modal für Wartezeiten -->
                <div class="modal fade" id="waitingTimesModal{{ $item['park']->id }}" tabindex="-1"
                     aria-labelledby="waitingTimesLabel{{ $item['park']->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="waitingTimesLabel{{ $item['park']->id }}">
                                    Wartezeiten: {{ $item['park']->name }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Schließen"></button>
                            </div>
                            <div class="modal-body p-4">
                                @if (!empty($item['waiting_times']))
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Attraktion</th>
                                                    <th>Wartezeit</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($item['waiting_times'] as $wait)
                                                    @php
                                                        $statusColor = match (true) {
                                                            $wait['waitingtime'] <= 10 => 'bg-success',
                                                            $wait['waitingtime'] <= 30 => 'bg-warning',
                                                            $wait['waitingtime'] > 30 => 'bg-danger',
                                                            default => 'bg-secondary',
                                                        };
                                                    @endphp
                                                    <tr class="animate__animated animate__fadeIn">
                                                        <td>{{ $wait['name'] }}</td>
                                                        <td>
                                                            <span class="badge {{ $statusColor }} px-3 py-2">
                                                                {{ $wait['waitingtime'] ?? 'N/A' }} Min
                                                            </span>
                                                        </td>
                                                        <td>{{ $wait['status'] === 'opened' ? 'Geöffnet' : 'Geschlossen' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-danger">Keine Wartezeiten verfügbar</p>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
    /* Gradient Background für Buttons */
    .btn-gradient {
        background: linear-gradient(45deg, #007bff, #00d4ff);
        border: none;
        transition: all 0.3s ease;
    }

    .btn-gradient:hover {
        background: linear-gradient(45deg, #0056b3, #00aaff);
        transform: translateY(-2px);
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        color: white;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    /* Info Grid */
    .info-grid {
        display: grid;
        gap: 0.5rem;
        text-align: left;
    }

    .info-item span:first-child {
        font-weight: 500;
        margin-right: 0.5rem;
    }

    /* Responsive Anpassungen */
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }

        .info-grid {
            text-align: center;
        }

        .table {
            font-size: 0.9rem;
        }
    }

    /* Divider */
    .divider {
        width: 100px;
        transition: width 0.3s ease;
    }

    .section:hover .divider {
        width: 150px;
    }

    /* Farben */
    .bg-success { background-color: #22c55e; color: white; }
    .bg-warning { background-color: #facc15; color: #1f2a44; }
    .bg-danger { background-color: #ef4444; color: white; }
    .bg-secondary { background-color: #6b7280; color: white; }
</style>
