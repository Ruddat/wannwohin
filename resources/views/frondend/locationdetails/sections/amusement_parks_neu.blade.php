<section class="parallax-section">
    <div class="container">
      <h2 class="display-5 fw-bold mb-5 text-black">Wilde Fahrten rund um {{ $location->title }}</h2>
      <div class="row g-4 justify-content-center">
        @foreach ($parks_with_opening_times as $item)
          @php
            $park = $item['park'];
            $embedHtml = null;
            $origin = request()->getSchemeAndHttpHost();

            if (!empty($park->embed_code)) {
                $embedHtml = $park->embed_code;
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
                    $embedHtml = '<video class="local-video" controls><source src="' . $videoUrl . '" type="video/mp4">Dein Browser unterstützt dieses Video nicht.</video>';
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

                  <div class="video-frame ratio ratio-16x9 mb-3">
                    {!! $embedHtml ?? '<div class="text-white text-center p-3">Kein Video verfügbar</div>' !!}
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

    .flip-card { width: 100%; height: 500px; perspective: 1000px; cursor: pointer; position: relative; transition: transform 0.3s; }
    .flip-card:hover { transform: scale(1.02); }
    .flip-card-inner { position: relative; width: 100%; height: 100%; transition: transform 0.8s; transform-style: preserve-3d; }
    .flip-card.flipped .flip-card-inner { transform: rotateY(180deg); }

    .flip-card-front, .flip-card-back {
      position: absolute; width: 100%; height: 100%; backface-visibility: hidden;
      border-radius: 1rem; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.3);
      display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 1.5rem;
    }

    .flip-card-front {
      background: rgba(255, 255, 255, 0.85); color: #333; z-index: 2;
      backdrop-filter: blur(6px); border: 1px solid rgba(255, 255, 255, 0.25);
    }

    .flip-card-back {
      background: #222; color: white; transform: rotateY(180deg); z-index: 1;
      align-items: stretch; justify-content: start;
    }

    .flip-card img {
      width: 100%; max-width: 280px; max-height: 180px; object-fit: contain; margin-bottom: 1rem;
    }

    .flip-card-back .description {
      font-size: 0.875rem; overflow-y: auto; max-height: 100px; margin-bottom: 1rem;
    }

    .video-frame {
      position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;
      border-radius: 0.5rem; background: #000;
    }

    .video-frame iframe, .video-frame video {
      position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    }

    @media (max-width: 576px) {
      .flip-card { height: 520px; }
    }
  </style>

<style>
    /* 🧊 Feedback Modal */
#parkFeedbackModal .modal-content {
  border-radius: 1rem;
  background-color: #fff;
  overflow: hidden;
  padding: 1.5rem;
}

#parkFeedbackModal .modal-header {
  border-bottom: none;
  background: linear-gradient(to right, #111, #333);
  color: white;
  border-top-left-radius: 1rem;
  border-top-right-radius: 1rem;
}

#parkFeedbackModal .modal-title {
  font-size: 1.25rem;
  font-weight: bold;
}

#parkFeedbackModal .modal-body {
  padding: 2rem 1rem 1.5rem;
}

#parkFeedbackModal textarea {
  resize: none;
  min-height: 100px;
}

#parkFeedbackModal button.btn {
  width: 100%;
  font-weight: bold;
  font-size: 1rem;
  padding: 0.6rem 1rem;
}

#parkFeedbackModal .form-range {
  width: 100%;
}
</style>



<script>
    const parksWithTimes = @json($parks_with_opening_times);

    document.addEventListener('DOMContentLoaded', function () {
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

      // Flip-Karten umdrehen + Video steuern
      document.querySelectorAll('.flip-card').forEach(card => {
        let flipped = false;
        const ytIframe = card.querySelector('iframe.yt-video');
        const localVideo = card.querySelector('video.local-video');

        card.addEventListener('click', function () {
          flipped = !flipped;
          card.classList.toggle('flipped');

          if (flipped) {
            if (ytIframe?.src.includes('youtube.com')) {
              ytIframe.contentWindow.postMessage(JSON.stringify({ event: "command", func: "playVideo", args: [] }), "*");
            } else if (localVideo) {
              localVideo.play();
            }
          } else {
            if (ytIframe?.src.includes('youtube.com')) {
              ytIframe.contentWindow.postMessage(JSON.stringify({ event: "command", func: "pauseVideo", args: [] }), "*");
            } else if (localVideo) {
              localVideo.pause();
              localVideo.currentTime = 0;
            }
          }
        });
      });
    });
  </script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
