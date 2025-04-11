<section class="parallax-section">
    <div class="container">
        <h2 class="display-5 fw-bold mb-5 text-black">Wilde Fahrten rund um {{ $location->title }}</h2>
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
        <div id="loading" class="text-center" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Laden...</span>
            </div>
        </div>
        <div class="row g-4 justify-content-center" id="parks-container">
            @foreach ($parks_with_opening_times as $item)
            @php
              $park = $item['park'];
              $embedHtml = null;
              $origin = request()->getSchemeAndHttpHost();

              if (!empty($park->embed_code)) {
      // Extrahiere die YouTube-Video-ID aus dem Embed-Code falls vorhanden
      if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $park->embed_code, $matches)) {
          $videoId = $matches[1];
          $embedHtml = '<iframe class="yt-video" src="https://www.youtube.com/embed/'.$videoId.'?enablejsapi=1&origin='.$origin.'&autoplay=0&mute=1" frameborder="0" allowfullscreen allow="autoplay; encrypted-media"></iframe>';
      } else {
          // Falls es ein anderer Embed-Code ist, unverändert übernehmen
          $embedHtml = $park->embed_code;
      }
  } elseif (!empty($park->video_url)) {
                  $videoUrl = $park->video_url;

                  if (str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be')) {
                      preg_match('/(youtu\.be\/|v=)([a-zA-Z0-9_-]+)/', $videoUrl, $matches);
                      $videoId = $matches[2] ?? null;
                      if ($videoId) {
                          $embedUrl = "https://www.youtube.com/embed/{$videoId}?enablejsapi=1&origin={$origin}&autoplay=0&mute=1";
                          $embedHtml = '<iframe class="yt-video" src="' . $embedUrl . '" frameborder="0" allowfullscreen allow="autoplay; encrypted-media"></iframe>';
                      }
                  } elseif (str_contains($videoUrl, 'vimeo.com')) {
                      $videoId = preg_replace('/[^0-9]/', '', basename($videoUrl));
                      $embedUrl = "https://player.vimeo.com/video/{$videoId}?autoplay=0&muted=1";
                      $embedHtml = '<iframe class="yt-video" src="' . $embedUrl . '" frameborder="0" allowfullscreen></iframe>';
                  } elseif (str_ends_with($videoUrl, '.mp4')) {
                      $embedHtml = '<video class="local-video" playsinline controls><source src="' . $videoUrl . '" type="video/mp4">Dein Browser unterstützt dieses Video nicht.</video>';
                  }
              }
            @endphp

            <div class="col-12 col-sm-6 col-lg-4">
              <div class="flip-card" data-park-id="{{ $park->id }}">
                <div class="flip-card-inner">
                  {{-- FRONT --}}
                  <div class="flip-card-front">
                    @if ($park->logo_url)
                      <img src="{{ asset($park->logo_url) }}" alt="{{ $park->name }}">
                    @endif
                    <h5>{{ $park->name }}</h5>
                    <p>{{ $park->slogan ?? 'Erlebe Abenteuer pur!' }}</p>

                    <div class="coolness w-100 mt-2">
                      <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" style="width: {{ $item['coolness_score'] ?? 90 }}%;"></div>
                      </div>
                      <small class="text-muted mt-1 d-block">Coolness-Faktor: {{ ($item['coolness_score'] ?? 90) / 10 }} / 10</small>
                    </div>

                    <a href="#" class="btn btn-sm btn-outline-primary mt-3 open-feedback-modal" data-park-id="{{ $park->id }}">
                      <i class="bi bi-chat-left-text me-1"></i> Bewerten
                    </a>
                  </div>

                  {{-- BACK --}}
                  <div class="flip-card-back">
                    <h5 class="mb-2">{{ $park->name }}</h5>

  <!-- Im Back-Teil der Karte (nach dem video-frame div) -->
  <div class="video-frame ratio ratio-16x9 mb-3">
      {!! $embedHtml ?? '<div class="text-white text-center p-3">Kein Video verfügbar</div>' !!}
      <button class="btn btn-sm btn-danger unmute-btn" style="position: absolute; bottom: 10px; right: 10px; display: none;">
        <i class="bi bi-volume-mute"></i> Ton aktivieren
      </button>
    </div>

                    <div class="description">{{ $park->description ?? 'Keine Beschreibung verfügbar.' }}</div>

                    <div class="mt-3 d-flex flex-wrap gap-2 justify-content-center">
                      @if (!empty($item['waiting_times']) && count($item['waiting_times']) > 0)
                      <a href="#" class="btn btn-warning fw-bold show-waittimes">
                          Wartezeiten anzeigen ({{ count($item['waiting_times']) }})
                        </a>
                    @endif

                      @if ($park->website)
                        <a href="{{ $park->website }}" target="_blank" class="btn btn-outline-light">Zur Website</a>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="waitTimeModal" tabindex="-1" aria-labelledby="waitTimeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-dark text-white">
              <h5 class="modal-title">Wartezeiten</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Attraktion</th>
                    <th>Wartezeit</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody id="waitTimeTableBody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    @include('components.modals.feedback')
</section>

<script>
    let parksWithTimes = @json($parks_with_opening_times);

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('radius').addEventListener('change', function() {
            const radius = this.value;
            const locationId = {{ $location->id }};
            const loading = document.getElementById('loading');
            loading.style.display = 'block';

            fetch(`/amusement-parks?location_id=${locationId}&radius=${radius}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('parks-container');
                container.innerHTML = data.html;
                parksWithTimes = data.parks;
                rebindEventListeners();
                loading.style.display = 'none';
            })
            .catch(error => {
                console.error('Fehler beim Laden der Parks:', error);
                alert('Fehler beim Laden der Parks. Bitte versuche es erneut.');
                loading.style.display = 'none';
            });
        });

        rebindEventListeners();
    });

    function rebindEventListeners() {
        // Feedback Modal öffnen
        document.querySelectorAll('.open-feedback-modal').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                e.stopPropagation();
                const parkId = btn.dataset.parkId;
                Livewire.dispatch('openParkFeedback', { parkId: parkId });
                const modal = new bootstrap.Modal(document.getElementById('parkFeedbackModal'));
                modal.show();
            });
        });

        // Wartezeiten anzeigen
        document.querySelectorAll('.btn-warning.show-waittimes').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                e.preventDefault();
                const card = btn.closest('.flip-card');
                const parkId = parseInt(card.dataset.parkId);
                const park = parksWithTimes.find(p => p.park.id === parkId);
                const list = park?.waiting_times ?? [];
                const tbody = document.getElementById('waitTimeTableBody');
                tbody.innerHTML = '';
                list.forEach(attraktion => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${attraktion.name}</td>
                        <td>${attraktion.waitingtime} min</td>
                        <td><span class="badge bg-${attraktion.status === 'open' ? 'success' : 'danger'}">${attraktion.status === 'open' ? 'Offen' : 'Geschlossen'}</span></td>
                    `;
                    tbody.appendChild(row);
                });
                const modal = new bootstrap.Modal(document.getElementById('waitTimeModal'));
                modal.show();
            });
        });

        // Flip-Karten Logik
        document.querySelectorAll('.flip-card').forEach(card => {
            let flipped = false;
            const videoFrame = card.querySelector('.video-frame');
            const ytIframe = videoFrame.querySelector('iframe.yt-video');
            const localVideo = videoFrame.querySelector('video.local-video');
            const unmuteBtn = videoFrame.querySelector('.unmute-btn');

            if (unmuteBtn) {
                unmuteBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (ytIframe) {
                        ytIframe.contentWindow.postMessage(JSON.stringify({
                            event: "command",
                            func: "unMute",
                            args: []
                        }), "*");
                        unmuteBtn.style.display = 'none';
                    } else if (localVideo) {
                        localVideo.muted = false;
                        unmuteBtn.style.display = 'none';
                    }
                });
            }

            card.addEventListener('click', function() {
                flipped = !flipped;
                card.classList.toggle('flipped');
                if (flipped) {
                    if (ytIframe) {
                        ytIframe.contentWindow.postMessage(JSON.stringify({
                            event: "command",
                            func: "playVideo",
                            args: []
                        }), "*");
                        setTimeout(() => {
                            ytIframe.contentWindow.postMessage(JSON.stringify({
                                event: "command",
                                func: "isMuted",
                                args: []
                            }), "*");
                        }, 1000);
                    } else if (localVideo) {
                        localVideo.play().catch(e => {
                            console.log("Video play error:", e);
                            if (localVideo.muted) {
                                unmuteBtn.style.display = 'block';
                            }
                        });
                    }
                } else {
                    if (ytIframe) {
                        ytIframe.contentWindow.postMessage(JSON.stringify({
                            event: "command",
                            func: "pauseVideo",
                            args: []
                        }), "*");
                    } else if (localVideo) {
                        localVideo.pause();
                        localVideo.currentTime = 0;
                    }
                }
            });
        });

    }
</script>
<style>
    .parallax-section {
      background-image: url('/assets/img/bpdw_kfn3_200619.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      padding: 80px 0;
      color: white;
      text-align: center;
    }

    .flip-card {
      width: 100%;
      height: 500px;
      perspective: 1000px;
      perspective-origin: center center;
      cursor: pointer;
      position: relative;
      transition: transform 0.3s;
      overflow: hidden;
      isolation: isolate;
      z-index: 0;
    }

    .flip-card:hover {
      transform: scale(1.02);
    }

    .flip-card-inner {
      position: relative;
      width: 100%;
      height: 100%;
      transition: transform 0.8s;
      transform-style: preserve-3d;
      will-change: transform;
    }

    .flip-card.flipped .flip-card-inner {
      transform: rotateY(180deg);
    }

    .flip-card-front,
    .flip-card-back {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      backface-visibility: hidden;
      -webkit-backface-visibility: hidden;
      transform-style: preserve-3d;
      border-radius: 1rem;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.3);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 1.5rem;
    }

    .flip-card-front {
      background: rgba(255, 255, 255, 0.85);
      color: #333;
      z-index: 2;
      backdrop-filter: blur(6px);
      border: 1px solid rgba(255, 255, 255, 0.25);
      transform: rotateY(0deg);
    }

    .flip-card-back {
      background: #222;
      color: white;
      transform: rotateY(180deg);
      z-index: 1;
      align-items: stretch;
      justify-content: start;
    }

    .flip-card img {
      width: 100%;
      max-width: 280px;
      max-height: 180px;
      object-fit: contain;
      margin-bottom: 1rem;
    }

    .flip-card-back .description {
      font-size: 0.875rem;
      overflow-y: auto;
      max-height: 100px;
      margin-bottom: 1rem;
    }

    .video-frame {
      position: relative;
      padding-bottom: 56.25%;
      height: 0;
      overflow: hidden;
      border-radius: 0.5rem;
      background: #000;
    }

    .video-frame iframe,
    .video-frame video {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
    }

    @media (max-width: 576px) {
      .flip-card {
        height: 520px;
      }
    }
  </style>
<style>
    .unmute-btn {
      z-index: 10;
      opacity: 0.8;
      transition: opacity 0.3s;
    }
    .unmute-btn:hover {
      opacity: 1;
    }
  </style>
