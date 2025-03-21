@if ($parks_with_opening_times->isNotEmpty())
<section id="freizeitparks" class="section m-0 py-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(245,245,245,0.95), rgba(220,220,220,0.95));">
    <div class="parallax-bg" style="background-image: url('/assets/img/bpdw_kfn3_200619.jpg');" data-jarallax data-speed="0.6"></div>
    <div class="container position-relative z-index-2 px-3 px-md-5">
        <!-- Überschrift und Umkreis-Auswahl -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="text-color-dark font-weight-extra-bold text-5xl mb-2 animate__animated animate__fadeInDown">
                    Freizeitparks nahe {{ $location->title }}
                </h2>
                <div class="divider w-24 mx-auto bg-gradient-to-r from-primary to-indigo-600 h-1 rounded-full transition-all duration-500 hover:w-48"></div>
            </div>
            <div class="col-12 text-center mt-4">
                <label for="radius" class="text-muted font-weight-semibold mr-3">Umkreis wählen:</label>
                <select id="radius" class="form-select d-inline-block w-auto rounded-full shadow-sm border-0 bg-white text-gray-700 transition-all duration-300 hover:shadow-md focus:ring-2 focus:ring-primary">
                    <option value="10" {{ request('radius', 150) == 10 ? 'selected' : '' }}>10 km</option>
                    <option value="20" {{ request('radius', 150) == 20 ? 'selected' : '' }}>20 km</option>
                    <option value="30" {{ request('radius', 150) == 30 ? 'selected' : '' }}>30 km</option>
                    <option value="50" {{ request('radius', 150) == 50 ? 'selected' : '' }}>50 km</option>
                    <option value="100" {{ request('radius', 150) == 100 ? 'selected' : '' }}>100 km</option>
                    <option value="150" {{ request('radius', 150) == 150 ? 'selected' : '' }}>150 km</option>
                </select>
            </div>
        </div>
        <!-- Container für die Park-Karten -->
        <div class="row g-4 justify-content-center" id="parks-container">
            @foreach ($parks_with_opening_times as $item)
                @include('frondend.locationdetails.components.park-card', ['item' => $item])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Das einzelne, dynamisch befüllte Modal -->
<div class="modal fade" id="waitingTimesModal" tabindex="-1" aria-labelledby="waitingTimesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-2xl rounded-xl overflow-hidden transition-all duration-300">
            <div class="modal-header bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-4">
                <h5 class="modal-title font-weight-bold text-lg tracking-wide" id="waitingTimesModalLabel">Wartezeiten</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>
            <div class="modal-body p-5">
                <!-- Der Inhalt wird dynamisch eingefügt -->
            </div>
            <div class="modal-footer bg-gray-50 border-t-0 p-4">
                <button type="button" class="btn btn-outline-primary btn-sm rounded-full px-4 py-2 shadow-sm" data-bs-dismiss="modal">
                    Schließen
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    // Event-Listener für den Radius-Wechsel (Ajax-Call bleibt unverändert)
    document.getElementById('radius').addEventListener('change', function() {
        const radius = this.value;
        const locationId = {{ $location->id }};
        fetch(`/amusement-parks?location_id=${locationId}&radius=${radius}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('parks-container');
            container.innerHTML = '';
            data.forEach(item => {
                container.innerHTML += renderParkCard(item);
            });
        })
        .catch(error => console.error('Fehler beim Laden der Parks:', error));
    });

    // Funktion zum Rendern einer Park-Karte (wie zuvor)
    function renderParkCard(item) {
        const logoHtml = item.park.logo_url
            ? `<img src="${item.park.logo_url}" alt="${item.park.name} Logo" class="h-10 w-auto rounded-full shadow-sm transition-transform duration-300 hover:scale-110">`
            : `<div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center"></div>`;

        let videoHtml = '';
        if (item.park.video_url) {
            if (item.park.video_url.includes('youtube.com')) {
                const videoId = item.park.video_url.includes('v=')
                    ? item.park.video_url.split('v=')[1].split('&')[0]
                    : item.park.video_url.split('/').pop();
                videoHtml = `<div class="video-frame rounded-lg overflow-hidden shadow-md border-2 border-gray-200 transition-transform duration-300 hover:scale-105">
                                <iframe width="100%" height="180" src="https://www.youtube.com/embed/${videoId}?autoplay=0&mute=1" frameborder="0" allowfullscreen></iframe>
                             </div>`;
            } else if (item.park.video_url.includes('vimeo.com')) {
                const videoId = item.park.video_url.split('/').pop();
                videoHtml = `<div class="video-frame rounded-lg overflow-hidden shadow-md border-2 border-gray-200 transition-transform duration-300 hover:scale-105">
                                <iframe width="100%" height="180" src="https://player.vimeo.com/video/${videoId}?autoplay=0&muted=1" frameborder="0" allowfullscreen></iframe>
                             </div>`;
            } else {
                videoHtml = `<div class="video-frame rounded-lg overflow-hidden shadow-md border-2 border-gray-200 transition-transform duration-300 hover:scale-105">
                                <video width="100%" height="180" controls>
                                    <source src="${item.park.video_url}" type="video/mp4">
                                    Ihr Browser unterstützt das Video-Tag nicht.
                                </video>
                             </div>`;
            }
        }

        const openingTimesHtml = item.opening_times ? `
            <div class="opening-times bg-gradient-to-r from-gray-50 to-gray-100 p-3 rounded-lg mb-3 transition-transform duration-300 hover:bg-gray-200 hover:shadow-md">
                <div class="status-badge ${item.opening_times.opened_today ? 'bg-emerald-500' : 'bg-rose-500'} text-white font-weight-bold px-3 py-1 rounded-full transition-transform duration-200 hover:scale-105">
                    ${item.opening_times.opened_today ? 'Geöffnet' : 'Geschlossen'}
                </div>
                <p class="mt-2 mb-0 text-sm text-gray-600">
                    <span class="font-medium">Öffnet:</span> ${item.opening_times.open_from || 'N/A'}<br>
                    <span class="font-medium">Schließt:</span> ${item.opening_times.closed_from || 'N/A'}
                </p>
            </div>` : '';

        const waitingTimesBtn = item.waiting_times && item.waiting_times.length ? `
            <button class="btn btn-gradient waiting-times-btn mt-3 font-weight-bold text-white rounded-full shadow-lg transition-all duration-300 hover:brightness-110 hover:shadow-xl"
                    data-park-name="${item.park.name}"
                    data-waiting-times='${JSON.stringify(item.waiting_times)}'
                    data-last-updated="${item.last_updated || 'N/A'}"
                    data-bs-toggle="modal" data-bs-target="#waitingTimesModal">
                Wartezeiten entdecken
            </button>` : '';

        return `
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card shadow-md border-0 h-100 transition-transform duration-500 hover:-translate-y-4 hover:shadow-2xl bg-white rounded-xl overflow-hidden">
                <div class="card-header bg-gradient-to-r from-gray-50 to-gray-100 p-3 flex justify-center items-center">
                    ${logoHtml}
                </div>
                <div class="card-body text-center p-4">
                    <h5 class="card-title text-primary font-weight-bold mb-3">${item.park.name}</h5>
                    ${videoHtml ? `<div class="mt-3">${videoHtml}</div>` : ''}
                    <div class="info-grid mb-4 text-gray-700">
                        <div class="info-item flex justify-between items-center">
                            <span class="text-muted font-weight-medium">Ort:</span>
                            <span>${item.park.country || 'Unbekannt'}</span>
                        </div>
                        <div class="info-item flex justify-between items-center">
                            <span class="text-muted font-weight-medium">Entfernung:</span>
                            <span>${Math.round(item.park.distance * 10) / 10} km</span>
                        </div>
                        <div class="info-item flex justify-between items-center">
                            <span class="text-muted font-weight-medium">Coolness:</span>
                            <div class="coolness-bar w-32 h-3 bg-gray-200 rounded-full overflow-hidden relative">
                                <div class="h-full rounded-full transition-all duration-700 ease-out" style="width: ${item.coolness_score}%; background: linear-gradient(45deg, #10b981, #3b82f6, #8b5cf6); box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);"></div>
                                <span class="absolute inset-0 flex items-center justify-center text-xs font-semibold text-white mix-blend-difference">${item.coolness_score}</span>
                            </div>
                        </div>
                    </div>
                    ${openingTimesHtml}
                    ${waitingTimesBtn}
                </div>
            </div>
        </div>`;
    }

    // Statt individuelle Button-Listener zu verwenden, fügen wir einen Listener zum Modal hinzu.
    // Bootstrap stellt in dem Event "show.bs.modal" das auslösende Element unter event.relatedTarget bereit.
    var waitingTimesModalEl = document.getElementById('waitingTimesModal');
    waitingTimesModalEl.addEventListener('show.bs.modal', function(event) {
        // Das Element, das das Modal ausgelöst hat.
        var button = event.relatedTarget;
        var parkName = button.getAttribute('data-park-name');
        var waitingTimesData = button.getAttribute('data-waiting-times');
        var lastUpdated = button.getAttribute('data-last-updated');
        var waitingTimes = JSON.parse(waitingTimesData);

        // Modal-Titel aktualisieren
        var modalTitle = waitingTimesModalEl.querySelector('.modal-title');
        modalTitle.textContent = 'Wartezeiten: ' + parkName;

        // HTML für den Tabelleninhalt erstellen
        var modalContent = `<p class="text-muted text-sm mb-4">Letzte Aktualisierung: ${lastUpdated} UTC</p>
                    <div class="table-responsive rounded-lg shadow-md overflow-hidden">
                        <table class="table table-hover align-middle bg-white border-0">
                            <thead class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800">
                                <tr>
                                    <th class="p-4 text-left font-semibold rounded-tl-lg">Attraktion</th>
                                    <th class="p-4 text-center font-semibold">Wartezeit</th>
                                    <th class="p-4 text-right font-semibold rounded-tr-lg">Status</th>
                                </tr>
                            </thead>
                            <tbody>`;
        waitingTimes.forEach(function(wait) {
            const statusColor = wait.waitingtime <= 10 ? 'bg-emerald-500 text-white' :
                                wait.waitingtime <= 30 ? 'bg-amber-400 text-gray-900' :
                                'bg-rose-500 text-white';
            modalContent += `<tr class="transition-colors duration-200 hover:bg-gray-50">
                                    <td class="p-4 text-left">${wait.name}</td>
                                    <td class="p-4 text-center">
                                        <span class="badge ${statusColor} px-4 py-2 rounded-full font-semibold shadow-sm">
                                            ${wait.waitingtime || 'N/A'} Min
                                        </span>
                                    </td>
                                    <td class="p-4 text-right font-medium">
                                        <span class="inline-block px-3 py-1 rounded-full ${wait.status === 'opened' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'}">
                                            ${wait.status === 'opened' ? 'Geöffnet' : 'Geschlossen'}
                                        </span>
                                    </td>
                                  </tr>`;
        });
        modalContent += `</tbody></table></div>`;
        waitingTimesModalEl.querySelector('.modal-body').innerHTML = modalContent;
    });
</script>

<style>
    /* Grundlegendes Styling – strukturiert und modular */
    .section {
        background: linear-gradient(135deg, rgba(245,245,245,0.95), rgba(131, 128, 128, 0.95));
        position: relative;
    }
    .parallax-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        opacity: 0.6;
        z-index: 1;
        transform: translateZ(0);
    }
    body.modal-open #freizeitparks .parallax-bg {
        filter: blur(10px);
        transition: filter 0.3s ease;
    }
    .z-index-2 { z-index: 2; }
    .divider {
        width: 96px;
        transition: width 0.5s ease;
    }
    .section:hover .divider { width: 192px; }
    .form-select {
        padding: 0.6rem 1.5rem;
        border: none;
        background: #fff;
        color: #374151;
        transition: all 0.3s ease;
    }
    .form-select:hover, .form-select:focus {
        box-shadow: 0 0 10px rgba(0,123,255,0.3);
        outline: none;
    }
    .card {
        background: rgba(255,255,255,0.98);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.5s ease;
    }
    .card:hover {
        transform: translateY(-16px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    .card-header {
        background: linear-gradient(to right, #f9fafb, #e5e7eb);
        padding: 0.75rem;
    }
    .video-frame {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .video-frame iframe, .video-frame video {
        display: block;
        border: none;
    }
    .info-grid {
        display: grid;
        gap: 1rem;
        text-align: left;
    }
    .info-item span:first-child {
        font-weight: 500;
        color: #4b5563;
    }
    .coolness-bar {
        position: relative;
        width: 128px;
        height: 12px;
        background: #e5e7eb;
        border-radius: 9999px;
        overflow: hidden;
    }
    .coolness-bar div {
        height: 100%;
        transition: width 0.7s ease-out;
    }
    .coolness-bar span {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
        color: #fff;
        mix-blend-mode: difference;
    }
    .btn-gradient {
        background: linear-gradient(45deg, #3b82f6, #8b5cf6);
        border: none;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(59,130,246,0.3);
    }
    .btn-gradient:hover {
        background: linear-gradient(45deg, #2563eb, #7c3aed);
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(59,130,246,0.5);
        filter: brightness(1.1);
    }
    .status-badge {
        display: inline-block;
        padding: 0.5rem 1.2rem;
        border-radius: 9999px;
        font-size: 0.9rem;
        transition: transform 0.2s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .status-badge:hover {
        transform: scale(1.05);
    }
    .modal-content {
        border-radius: 1rem;
        box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        transform: scale(0.95);
        transition: transform 0.3s ease;
    }
    .modal.show .modal-content {
        transform: scale(1);
    }
    .modal-header {
        border-bottom: none;
        padding: 1.5rem 2rem;
        background: linear-gradient(to right, #4f46e5, #9333ea);
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
    .modal-backdrop.show {
        opacity: var(--bs-backdrop-opacity);
        z-index: 1;
    }
    .btn-outline-primary {
        border: 2px solid #3b82f6;
        color: #3b82f6;
        transition: all 0.3s ease;
    }
    .btn-outline-primary:hover {
        background-color: #3b82f6;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(59,130,246,0.3);
    }
    .bg-emerald-500 { background-color: #10b981; }
    .bg-rose-500 { background-color: #ef4444; }
    .bg-amber-400 { background-color: #fbbf24; }
    .bg-emerald-100 { background-color: #d1fae5; }
    .text-emerald-700 { color: #047857; }
    .bg-rose-100 { background-color: #fee2e2; }
    .text-rose-700 { color: #be123c; }
    @media (max-width: 768px) {
        .card-body { padding: 1.5rem; }
        .info-grid { text-align: center; }
        .coolness-bar { width: 100px; }
    }
    @media (max-width: 576px) {
        .card-body { padding: 1rem; }
        .btn-gradient { font-size: 0.9rem; padding: 0.6rem 1.2rem; }
        .status-badge { font-size: 0.8rem; padding: 0.4rem 1rem; }
        .modal-body { padding: 1rem; max-height: 60vh; }
    }
</style>
