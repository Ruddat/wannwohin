@foreach ($parksWithOpeningTimes as $item)
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
