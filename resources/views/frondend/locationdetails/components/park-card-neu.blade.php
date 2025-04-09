<!-- HTML -->
<div class="col-12 col-sm-6 col-md-4">
<div class="product-card" id="{{ $parkData['park']->id }}" onclick="this.classList.toggle('flipped')">
    <div class="product-card__inner">
      <div class="product-card__front">
        <div class="product-card__image" style="background-image: url('{{ asset($parkData['park']->logo_url) }}');"></div>
        <div class="product-card__content">
          <h2 class="product-card__title">{{ $parkData['park']->name }}</h2>
          <p class="product-card__description">
            A stand-on with an exceptional compact stance. Great for tight spaces and trailering.
          </p>
        </div>
      </div>
      <div class="product-card__back">
        <div class="product-card__content">
          <h2 class="product-card__title">At a glance</h2>
          <div class="product-card__reviews">
            <div class="product-card__stars">
              <svg class="star-icon" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
              <svg class="star-icon" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
              <svg class="star-icon" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
              <svg class="star-icon" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
              <svg class="star-icon half" viewBox="0 0 24 24"><path d="M22 9.24l-7.19-.62L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.63-7.03L22 9.24zM12 15.4V6.1l1.71 4.04 4.38.38-3.32 2.88 1 4.28L12 15.4z"/></svg>
            </div>
            <p class="product-card__review-count">23 Reviews</p>
          </div>
          @if ($parkData['park']->embed_code)
          <!-- Embed-Code direkt einbetten -->
          <div class="mt-3 relative">
              <div class="video-frame rounded-lg overflow-hidden shadow-md border border-gray-200 transition-transform duration-300 hover:scale-105">
                  {!! $item['park']->embed_code !!}
              </div>
          </div>
      @elseif ($parkData['park']->video_url)
          <!-- Fallback auf video_url -->
          <div class="mt-3 relative">
              <div class="video-frame rounded-lg overflow-hidden shadow-md border border-gray-200 transition-transform duration-300 hover:scale-105">
                  @if (str_contains($parkData['park']->video_url, 'youtube.com'))
                      @php
                          $videoId = str_contains($parkData['park']->video_url, 'v=')
                              ? explode('v=', $parkData['park']->video_url)[1]
                              : basename($parkData['park']->video_url);
                          $videoId = strtok($videoId, '&');
                          $embedUrl = "https://www.youtube.com/embed/{$videoId}?autoplay=0&mute=1";
                      @endphp
                      <iframe width="100%" height="180" src="{{ $embedUrl }}" frameborder="0" allowfullscreen></iframe>
                  @elseif (str_contains($item['park']->video_url, 'vimeo.com'))
                      @php
                          $videoId = basename($parkData['park']->video_url);
                          $embedUrl = "https://player.vimeo.com/video/{$videoId}?autoplay=0&muted=1";
                      @endphp
                      <iframe width="100%" height="180" src="{{ $embedUrl }}" frameborder="0" allowfullscreen></iframe>
                  @else
                      <video width="100%" height="180" controls class="rounded-lg">
                          <source src="{{ $parkData['park']->video_url }}" type="video/mp4">
                          Ihr Browser unterstützt das Video-Tag nicht.
                      </video>
                  @endif
              </div>
          </div>
      @endif

          <ul class="product-card__features">
            <li>Manage backyard gates with ease with the 36" deck option</li>
            <li>Your choice of deck sizes ranging from 36", 48", 52" and 60"</li>
            <li>Updated hip bolstering offers superior operator comfort</li>
          </ul>
          @if (!empty($item['waiting_times']))
          <button class="product-card__button"
                  data-park-name="{{ $parkData['park']->name }}"
                  data-waiting-times='@json($parkData['waiting_times'])'
                  data-last-updated="{{ $parkData['last_updated'] ?? 'N/A' }}"
                  data-bs-toggle="modal" data-bs-target="#waitingTimesModal">
              Wartezeiten entdecken
              <svg class="arrow-icon" viewBox="0 0 24 24"><path d="M8.59 16.34l4.58-4.59-4.58-4.59L10 5.75l6 6-6 6z"/></svg>

          </button>
      @endif
        </div>
      </div>
    </div>
  </div>
</div>

<style>
:root {
  --text-dark: #333;
  --text-light: #777;
  --star-color: #FFB714;
}

.product-card {
  width: 300px;
  height: 400px;
  perspective: 1000px;
  margin: 8px;
  font-family: "Roboto", sans-serif;
  transition: transform 0.3s ease;
}

.product-card:hover {
  transform: scale(1.05);
}

.product-card__inner {
  position: relative;
  width: 100%;
  height: 100%;
  transform-style: preserve-3d;
  transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.product-card.flipped .product-card__inner {
  transform: rotateY(180deg);
}

.product-card__front,
.product-card__back {
  position: absolute;
  width: 100%;
  height: 100%;
  backface-visibility: hidden;
  background: white;
  border-radius: 5px;
  box-shadow: 0 14px 50px -4px rgba(0, 0, 0, 0.15);
}

.product-card__back {
  transform: rotateY(180deg);
}

.product-card__image {
  height: 250px;
  background-size: cover;
  background-position: center;
  border-radius: 5px 5px 0 0;
}

.product-card__content {
  padding: 16px;
}

.product-card__title {
  font-family: "Oswald", sans-serif;
  font-size: 27px;
  font-weight: 500;
  color: var(--text-dark);
  text-transform: uppercase;
  margin-bottom: 10px;
}

.product-card__description {
  color: var(--text-light);
  line-height: 22px;
}

.product-card__reviews {
  display: flex;
  align-items: center;
  margin: 12px 0;
}

.product-card__stars {
  display: flex;
}

.star-icon {
  width: 24px;
  height: 24px;
  fill: var(--star-color);
  margin-right: 2px;
}

.product-card__review-count {
  color: #c4c4c4;
  font-weight: 300;
  margin-left: 6px;
}

.product-card__features {
  list-style: disc outside;
  padding-left: 20px;
  color: var(--text-light);
  margin-bottom: 16px;
}

.product-card__button {
  position: absolute;
  bottom: 16px;
  width: calc(100% - 32px);
  height: 56px;
  background: linear-gradient(-90deg, #FFB714, #FFE579);
  border: none;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.product-card__button span {
  font-family: "Oswald", sans-serif;
  color: var(--text-dark);
  text-transform: uppercase;
  transition: transform 0.3s ease;
}

.arrow-icon {
  width: 24px;
  height: 24px;
  fill: var(--text-dark);
  margin-left: 8px;
  opacity: 0;
  transform: translateX(-8px);
  transition: all 0.3s ease;
}

.product-card__button:hover span {
  transform: translateX(-4px);
}

.product-card__button:hover .arrow-icon {
  opacity: 1;
  transform: translateX(0);
}
</style>

<script>
document.querySelector('.product-card').addEventListener('click', function() {
  this.classList.toggle('flipped');
});
</script>
