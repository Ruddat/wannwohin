<section class="parallax-section">
    <div class="container">
<h2 class="display-5 fw-bold mb-5 text-white parallax-heading parallax-title">
    Freizeitparks und Zoos rund um  {{ $location->title }}
</h2>

        <div class="col-12 text-center mb-4">
            <div class="radius-selector-wrapper">
                <label for="radius" class="radius-label">
                    <i class="bi bi-geo-alt-fill me-2"></i> Umkreis wählen
                </label>
                <select id="radius" class="radius-select">
                    <option value="10" {{ request('radius', 150) == 10 ? 'selected' : '' }}>10 km</option>
                    <option value="20" {{ request('radius', 150) == 20 ? 'selected' : '' }}>20 km</option>
                    <option value="30" {{ request('radius', 150) == 30 ? 'selected' : '' }}>30 km</option>
                    <option value="50" {{ request('radius', 150) == 50 ? 'selected' : '' }}>50 km</option>
                    <option value="100" {{ request('radius', 150) == 100 ? 'selected' : '' }}>100 km</option>
                    <option value="150" {{ request('radius', 150) == 150 ? 'selected' : '' }}>150 km</option>
                </select>
            </div>
        </div>
        <div id="loading" class="text-center" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Laden...</span>
            </div>
        </div>
        <div class="row g-4 justify-content-center" id="parks-container">
            @php
               // dd($parks_with_opening_times);
            @endphp
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
                            <div class="progress-bar bg-success" style="width: {{ $item['coolness_score'] ?? 0 }}%;"></div>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            Coolness-Faktor:
                            @if ($item['coolness_score'])
                                @php
                                    $coolness = $item['coolness_score'] / 10; // Skala 0-10
                                    $smiley = $coolness >= 8 ? '🔥' : ($coolness >= 6 ? '😎' : ($coolness >= 4 ? '😊' : '😐'));
                                @endphp
                                <span class="coolness-smilies">{{ $smiley }}</span> {{ $coolness }} / 10
                                ({{ $item['vote_count'] ?? count(DB::table('park_coolness_votes')->where('park_id', $park->id)->get()) }} Stimmen)
                            @else
                                <span class="coolness-smilies">🤔</span> N/A
                            @endif
                        </small>
                    </div>

                    <div class="rating mt-2">
                        <small class="text-muted">
                            Bewertung:
                            @if ($item['avg_rating'])
                                @php
                                    $rating = $item['avg_rating']; // Skala 0-5
                                    $fullStars = floor($rating); // Volle Sterne
                                    $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0; // Halber Stern
                                    $emptyStars = 5 - $fullStars - $halfStar; // Leere Sterne
                                @endphp
                                <span class="rating-stars">
                                    @for ($i = 0; $i < $fullStars; $i++)
                                        <span class="star filled">★</span>
                                    @endfor
                                    @if ($halfStar)
                                        <span class="star half-filled">★</span>
                                    @endif
                                    @for ($i = 0; $i < $emptyStars; $i++)
                                        <span class="star">★</span>
                                    @endfor
                                </span>
                                {{ $rating }} / 5 ({{ $item['comment_count'] }} Kommentare)
                            @else
                                <span class="rating-stars">
                                    @for ($i = 0; $i < 5; $i++)
                                        <span class="star">★</span>
                                    @endfor
                                </span>
                                Noch keine Bewertungen
                            @endif
                        </small>
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

    <div class="description @if (empty($item['waiting_times']) || count($item['waiting_times']) == 0) expanded @endif">
        {{ $park->description ?? 'Keine Beschreibung verfügbar.' }}
    </div>
    <div class="mt-3 d-flex flex-wrap gap-2 justify-content-center">
        @if (!empty($item['waiting_times']) && count($item['waiting_times']) > 0)
            <a href="#" class="btn btn-warning fw-bold show-waittimes">
                Wartezeiten anzeigen ({{ count($item['waiting_times']) }})
            </a>
        @endif
        @if ($park->url)
            <a href="{{ $park->url }}" target="_blank" class="btn btn-outline-light">Zur Website</a>
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
            // Akzeptiere sowohl "open" als auch "opened" als offenen Status
            const isOpen = attraktion.status === 'open' || attraktion.status === 'opened';
            row.innerHTML = `
                <td>${attraktion.name}</td>
                <td>${attraktion.waitingtime} min</td>
                <td><span class="badge bg-${isOpen ? 'success' : 'danger'}">${isOpen ? 'Offen' : 'Geschlossen'}</span></td>
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
    max-height: 100px; /* Standardhöhe, wenn Wartezeiten vorhanden sind */
    margin-bottom: 1rem;
    transition: max-height 0.3s ease; /* Sanfte Übergangsanimation */
}

.flip-card-back .description.expanded {
    max-height: 200px; /* Größere Höhe, wenn keine Wartezeiten vorhanden sind */
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

/* LOGO CONTAINER – saubere Aspect Ratio */
.logo-wrapper {
    width: 100%;
    max-width: 220px;
    aspect-ratio: 4 / 3;
    background: rgba(255, 255, 255, 0.85);
    border-radius: 12px;
    padding: 10px;
    overflow: hidden;

    display: flex;
    justify-content: center;
    align-items: center;

    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border: 1px solid rgba(255,255,255,0.6);
}

.logo-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: contain !important;
}

/* Titel */
.park-title {
    font-weight: 700;
    margin-top: 15px;
    font-size: 1.25rem;
    text-align: center;
    color: #222;
}

/* Slogan */
.park-slogan {
    font-size: 0.95rem;
    color: #555;
    margin-bottom: 10px;
    text-align: center;
}


  </style>
<style>
    .radius-selector-wrapper {
    display: inline-flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 50px;
    padding: 8px 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(8px);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.radius-selector-wrapper:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
}

.radius-label {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin-right: 12px;
    display: flex;
    align-items: center;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.radius-label i {
    color: #ff6200; /* Orangeton für Abenteuer-Stimmung */
    font-size: 1.3rem;
    transition: transform 0.3s ease;
}

.radius-selector-wrapper:hover .radius-label i {
    transform: rotate(360deg); /* Kleiner spielerischer Effekt */
}

.radius-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background: linear-gradient(135deg, #ff6200, #ff8c00);
    color: rgb(0, 0, 0);
    border: none;
    border-radius: 25px;
    padding: 10px 24px 10px 16px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    outline: none;
    position: relative;
    transition: background 0.3s ease, transform 0.3s ease;
}

.radius-select:hover {
    background: linear-gradient(135deg, #e55a00, #e67e22);
    transform: scale(1.05);
}

.radius-select:focus {
    box-shadow: 0 0 0 3px rgba(255, 98, 0, 0.3);
}

/* Pfeil für das Select-Element */
.radius-selector-wrapper::after {
    content: '\25BC'; /* Unicode für Abwärtspfeil */
    color: white;
    font-size: 0.8rem;
    position: absolute;
    right: 24px;
    pointer-events: none;
    transition: transform 0.3s ease;
}

.radius-select:focus + .radius-selector-wrapper::after {
    transform: rotate(180deg);
}

/* Responsive Anpassungen */
@media (max-width: 576px) {
    .radius-selector-wrapper {
        flex-direction: column;
        padding: 12px;
        width: 100%;
        border-radius: 32px;
    }

    .radius-label {
        margin-right: 0;
        margin-bottom: 8px;
    }

    .radius-select {
        width: 80%;
        text-align: center;
    }
}
</style>
<style>
    /* Coolness Smilies */
.coolness-smilies {
    font-size: 1.25rem;
    margin-right: 5px;
    vertical-align: middle;
}

/* Rating Sterne */
.rating-stars {
    display: inline-flex;
    align-items: center;
    font-size: 1rem;
    color: #ccc; /* Grau für leere Sterne */
}

.rating-stars .star {
    margin-right: 2px;
}

.rating-stars .filled {
    color: #ffca08; /* Gold für gefüllte Sterne */
}

.rating-stars .half-filled {
    position: relative;
    display: inline-block;
}

.rating-stars .half-filled::before {
    content: "\2605"; /* Voller Stern */
    position: absolute;
    left: 0;
    width: 50%;
    overflow: hidden;
    color: #ffca08; /* Gold für halb gefüllte Sterne */
}

.rating-stars .half-filled::after {
    content: "\2605"; /* Voller Stern */
    color: #ccc; /* Grau für den Rest */
}


.parallax-heading {
    display: inline-block;
    background: rgba(0,0,0,0.55);
    padding: 12px 28px;
    border-radius: 12px;
    backdrop-filter: blur(4px);
}

.parallax-title {
    text-shadow: 0 3px 12px rgba(0,0,0,0.8);
}
</style>
