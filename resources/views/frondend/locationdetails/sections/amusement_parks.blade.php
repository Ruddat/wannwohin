@if ($parks_with_opening_times->isNotEmpty())
<section id="freizeitparks" class="section section-no-border m-0 py-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(240, 240, 240, 0.9), rgba(200, 200, 200, 0.9));">
    <!-- Parallax-Hintergrund -->
    <div class="parallax-bg"
         style="background-image: url('/assets/img/bpdw_kfn3_200619.jpg');"
         data-jarallax
         data-speed="0.5">
    </div>

    <div class="container position-relative z-index-2 px-3 px-md-5">
        <!-- Überschrift -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="text-color-dark font-weight-extra-bold text-5xl mb-2 animate__animated animate__fadeInDown">
                    Freizeitparks nahe {{ $location->title }}
                </h2>
                <div class="divider w-20 mx-auto bg-primary h-1 rounded transition-all duration-300"></div>
            </div>
        </div>

        <!-- Karten -->
        <div class="row g-4 justify-content-center">
            @foreach ($parks_with_opening_times as $item)
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card shadow-lg border-0 h-100 transform hover:-translate-y-3 hover:shadow-xl transition-all duration-500 bg-white rounded-lg overflow-hidden">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title text-primary font-weight-bold mb-3 animate__animated animate__fadeInUp">
                                {{ $item['park']->name }}
                            </h5>
                            <div class="info-grid mb-3 text-gray-700">
                                <div class="info-item">
                                    <span class="text-muted font-weight-medium">Ort:</span>
                                    <span>{{ $item['park']->country ?? 'Unbekannt' }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="text-muted font-weight-medium">Entfernung:</span>
                                    <span>{{ round($item['park']->distance, 1) }} km</span>
                                </div>
                            </div>

                            @if ($item['opening_times'])
                                <div class="opening-times bg-gray-100 p-3 rounded-lg mb-3 transform transition-all duration-300 hover:bg-gray-200">
                                    <div class="status-badge {{ $item['opening_times']['opened_today'] ? 'bg-success' : 'bg-danger' }} text-white font-weight-bold">
                                        {{ $item['opening_times']['opened_today'] ? 'Geöffnet' : 'Geschlossen' }}
                                    </div>
                                    <p class="mt-2 mb-0 text-sm">
                                        <span class="text-muted">Öffnet:</span> {{ $item['opening_times']['open_from'] ?? 'N/A' }}<br>
                                        <span class="text-muted">Schließt:</span> {{ $item['opening_times']['closed_from'] ?? 'N/A' }}
                                    </p>
                                </div>
                            @else
                                <p class="text-danger text-sm font-italic">Keine Öffnungszeiten verfügbar</p>
                            @endif

                            @if (!empty($item['waiting_times']))
                                <button class="btn btn-primary btn-gradient w-100 mt-2 font-weight-bold"
                                        data-bs-toggle="modal"
                                        data-bs-target="#waitingTimesModal{{ $item['park']->id }}">
                                    Wartezeiten anzeigen
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Modal für Wartezeiten -->
                <div class="modal fade" id="waitingTimesModal{{ $item['park']->id }}" tabindex="-1"
                     aria-labelledby="waitingTimesLabel{{ $item['park']->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
                        <div class="modal-content border-0 shadow-2xl rounded-xl overflow-hidden transform transition-all duration-300 animate__animated animate__zoomIn">
                            <!-- Header -->
                            <div class="modal-header bg-gradient-to-r from-primary via-blue-500 to-indigo-600 text-white p-4 animate__animated animate__fadeInDown">
                                <h5 class="modal-title font-weight-bold text-lg tracking-wide" id="waitingTimesLabel{{ $item['park']->id }}">
                                    Wartezeiten: {{ $item['park']->name }}
                                </h5>
                                <button type="button" class="btn-close btn-close-white opacity-80 hover:opacity-100 transition-opacity"
                                        data-bs-dismiss="modal" aria-label="Schließen"></button>
                            </div>
                            <!-- Body -->
                            <div class="modal-body p-5 bg-gradient-to-b from-gray-50 to-white">
                                @if (!empty($item['waiting_times']))
                                    <div class="table-responsive rounded-lg shadow-md overflow-hidden">
                                        <table class="table table-hover align-middle bg-white border-0">
                                            <thead class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800">
                                                <tr>
                                                    <th class="p-4 text-left font-semibold rounded-tl-lg">Attraktion</th>
                                                    <th class="p-4 text-center font-semibold">Wartezeit</th>
                                                    <th class="p-4 text-right font-semibold rounded-tr-lg">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($item['waiting_times'] as $wait)
                                                    @php
                                                        $statusColor = match (true) {
                                                            $wait['waitingtime'] <= 10 => 'bg-success text-white',
                                                            $wait['waitingtime'] <= 30 => 'bg-warning text-gray-900',
                                                            $wait['waitingtime'] > 30 => 'bg-danger text-white',
                                                            default => 'bg-secondary text-white',
                                                        };
                                                    @endphp
                                                    <tr class="animate__animated animate__fadeInUp animate__faster hover:bg-gray-50 transition-colors duration-200">
                                                        <td class="p-4 text-left">{{ $wait['name'] }}</td>
                                                        <td class="p-4 text-center">
                                                            <span class="badge {{ $statusColor }} px-4 py-2 rounded-full font-semibold shadow-sm transform transition-transform hover:scale-105">
                                                                {{ $wait['waitingtime'] ?? 'N/A' }} Min
                                                            </span>
                                                        </td>
                                                        <td class="p-4 text-right font-medium">
                                                            <span class="inline-block px-3 py-1 rounded-full {{ $wait['status'] === 'opened' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                                {{ $wait['status'] === 'opened' ? 'Geöffnet' : 'Geschlossen' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-danger font-italic text-center animate__animated animate__fadeIn">Keine Wartezeiten verfügbar</p>
                                @endif
                            </div>
                            <!-- Footer -->
                            <div class="modal-footer bg-gray-50 border-t-0 p-4">
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-full px-4 py-2" data-bs-dismiss="modal">
                                    Schließen
                                </button>
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
/* Parallax-Hintergrund */
.parallax-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0.5;
    z-index: 1;
    transform: translateZ(0);
    will-change: transform;
}

/* Wenn ein Modal geöffnet ist, blurriere nur den Parallax-Hintergrund */
body.modal-open #freizeitparks .parallax-bg {
    filter: blur(10px);
}

/* Container-Z-Index */
.z-index-2 {
    z-index: 2;
}

    /* Sektion */
    .section {
        background: linear-gradient(135deg, rgba(240, 240, 240, 0.9), rgba(200, 200, 200, 0.9));
        position: relative;
    }

    /* Gradient Button */
    .btn-gradient {
        background: linear-gradient(45deg, #007bff, #00d4ff);
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
    }

    .btn-gradient:hover {
        background: linear-gradient(45deg, #0056b3, #00aaff);
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0, 123, 255, 0.5);
    }

    /* Status Badge */
    .status-badge {
        display: inline-block;
        padding: 0.5rem 1.2rem;
        border-radius: 20px;
        font-size: 0.9rem;
        transition: transform 0.3s ease;
    }

    .status-badge:hover {
        transform: scale(1.1);
    }

    /* Info Grid */
    .info-grid {
        display: grid;
        gap: 0.75rem;
        text-align: left;
    }

    .info-item span:first-child {
        font-weight: 500;
        margin-right: 0.5rem;
        color: #4b5563;
    }

    /* Divider */
    .divider {
        width: 100px;
        transition: width 0.4s ease;
    }

    .section:hover .divider {
        width: 200px;
    }

    /* Karten-Styling */
    .card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.5s ease;
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
    }

    /* Farben */
    .bg-success { background-color: #22c55e; color: white; }
    .bg-warning { background-color: #facc15; color: #1f2a44; }
    .bg-danger { background-color: #ef4444; color: white; }
    .bg-secondary { background-color: #6b7280; color: white; }

    /* Modal Styling */
    .modal-dialog {
        max-width: 90%;
        margin: 1rem auto;
    }

    .modal-content {
        border-radius: 1rem;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        transform: scale(0.95);
        transition: transform 0.3s ease;
    }

    .modal.show .modal-content {
        transform: scale(1);
    }

    .modal-header {
        border-bottom: none;
        padding: 1.5rem 2rem;
        background: linear-gradient(to right, #007bff, #4f46e5);
        color: white;
    }

    .modal-body {
        padding: 2rem;
        background: linear-gradient(to bottom, #f9fafb, #ffffff);
        overflow-y: auto;
        max-height: 70vh;
    }

    .modal-footer {
        border-top: none;
        justify-content: center;
    }

    .table-responsive {
        overflow-x: auto;
    }

    /* Tabelle */
    .table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table th {
        background: linear-gradient(to right, #f3f4f6, #e5e7eb);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .table td {
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: #f9fafb;
    }

    /* Badges */
    .badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease;
    }

    .badge:hover {
        transform: scale(1.05);
    }

    /* Statusanzeige */
    .bg-green-100 { background-color: #dcfce7; }
    .text-green-700 { color: #15803d; }
    .bg-red-100 { background-color: #fee2e2; }
    .text-red-700 { color: #b91c1c; }

    /* Button im Footer */
    .btn-outline-primary {
        border: 2px solid #007bff;
        color: #007bff;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background-color: #007bff;
        color: white;
        transform: translateY(-2px);
    }

    /* Responsive Anpassungen */
    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem;
        }

        .info-grid {
            text-align: center;
        }

        .table {
            font-size: 0.9rem;
        }

        .divider {
            width: 80px;
        }

        .section:hover .divider {
            width: 120px;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .table th, .table td {
            padding: 0.75rem;
        }
    }

    @media (max-width: 576px) {
        .card-body {
            padding: 1rem;
        }

        .btn-gradient {
            font-size: 0.9rem;
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 0.4rem 1rem;
        }

        .modal-dialog {
            max-width: 100%;
            margin: 0.5rem;
        }

        .modal-body {
            padding: 1rem;
            max-height: 60vh;
        }

        .table {
            font-size: 0.85rem;
        }

        .table th, .table td {
            padding: 0.5rem;
        }

        .badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }
    }
</style>

{{--
in npm
<!-- Jarallax Library einbinden -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jarallax/1.12.6/jarallax.min.js"></script>
<script>
    jarallax(document.querySelectorAll('[data-jarallax]'), {
        speed: 0.5
    });
</script>
--}}
