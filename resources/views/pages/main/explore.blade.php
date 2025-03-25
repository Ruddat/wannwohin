@extends('layouts.main')

@section('content')


<section class="explore-section py-5">
<div class="container">
    <h1 class="text-center text-color-dark mb-4 animate__animated animate__fadeIn">
        @autotranslate('Finde dein Abenteuer!', app()->getLocale())
    </h1>
    <p class="text-center text-color-grey mb-5 animate__animated animate__fadeIn animate__delay-1s">
        @autotranslate('Beantworte diese Fragen, und wir schlagen dir spontan ein Ziel vor!', app()->getLocale())
    </p>

    <form action="{{ route('explore.results') }}" method="GET" class="row g-4 justify-content-center">
        <!-- Frage 1: Aktivität -->
        <div class="col-md-10">
            <label class="form-label fw-bold text-center d-block mb-3 animate__animated animate__fadeInUp">
                @autotranslate('Was möchtest du erleben?', app()->getLocale())
            </label>
            <div class="activity-options d-flex flex-wrap justify-content-center gap-3">
                <label class="activity-card">
                    <input type="radio" name="activity" value="relax" class="activity-input" required>
                    <div class="activity-content">
                        <i class="fas fa-umbrella-beach fa-2x"></i>
                        <span>@autotranslate('Entspannen', app()->getLocale())</span>
                    </div>
                </label>
                <label class="activity-card">
                    <input type="radio" name="activity" value="adventure" class="activity-input">
                    <div class="activity-content">
                        <i class="fas fa-hiking fa-2x"></i>
                        <span>@autotranslate('Abenteuer', app()->getLocale())</span>
                    </div>
                </label>
                <label class="activity-card">
                    <input type="radio" name="activity" value="culture" class="activity-input">
                    <div class="activity-content">
                        <i class="fas fa-landmark fa-2x"></i>
                        <span>@autotranslate('Kultur', app()->getLocale())</span>
                    </div>
                </label>
                <label class="activity-card">
                    <input type="radio" name="activity" value="amusement" class="activity-input">
                    <div class="activity-content">
                        <i class="ti ti-rollercoaster" style="font-size: 2.5rem;"></i>
                        <span>@autotranslate('Freizeitparks', app()->getLocale())</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Frage 2: Zeitpunkt -->
        <div class="col-md-10 mt-5">
            <label class="form-label fw-bold text-center d-block mb-3 animate__animated animate__fadeInUp">
                @autotranslate('Wann möchtest du reisen?', app()->getLocale())
            </label>
            <div class="time-options d-flex flex-wrap justify-content-center gap-3">
                <label class="time-card">
                    <input type="radio" name="time" value="now" class="time-input" required>
                    <div class="time-content">
                        <i class="fas fa-clock fa-2x"></i>
                        <span>@autotranslate('Jetzt', app()->getLocale())</span>
                    </div>
                </label>
                <label class="time-card">
                    <input type="radio" name="time" value="month" class="time-input">
                    <div class="time-content">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                        <span>@autotranslate('Nächster Monat', app()->getLocale())</span>
                    </div>
                </label>
                <label class="time-card">
                    <input type="radio" name="time" value="later" class="time-input">
                    <div class="time-content">
                        <i class="fas fa-hourglass-half fa-2x"></i>
                        <span>@autotranslate('Später', app()->getLocale())</span>
                    </div>
                </label>
            </div>
        </div>

        @if ($latitude && $longitude)
            <input type="hidden" name="lat" value="{{ $latitude }}">
            <input type="hidden" name="lon" value="{{ $longitude }}">
        @endif

        <div class="col-md-10 text-center mt-5">
            <button type="submit" class="btn btn-explore animate__animated animate__pulse animate__infinite">
                @autotranslate('Ergebnisse anzeigen', app()->getLocale())
            </button>
        </div>
    </form>
</div>
</section>


<div class="destination-slider-wrapper">
  <section class="destination-slider-section">
    <div class="ds-container">
      <header class="ds-header">
        <h1 class="ds-title">
          @autotranslate('Meistbesuchte Reiseziele', app()->getLocale())
        </h1>
      </header>

      <!-- Swiper Slider -->
      <div class="ds-slider swiper" id="destinationSlider">
        <div class="swiper-wrapper">
          @php
            $carouselItems = $popularLocations->count() < 3 ? $popularLocations->concat($popularLocations)->take(10) : $popularLocations;
            $randomImages = [
                asset('img/locations/woman-meditating-beach-with-copy-space.jpg'),
                asset('img/locations/medium-shot-man-exploring-with-map.jpg'),
                asset('img/locations/beach-area-blurred-night.jpg'),
            ];
          @endphp
          @foreach ($carouselItems as $popularLocation)
          <div class="swiper-slide">
            <div class="ds-card">
              <div class="ds-card-content">
                <header class="ds-card-header">
                  <h2 class="ds-card-title" data-title="{{ $popularLocation->title ?? $popularLocation['title'] }}">
                    {{ $popularLocation->title ?? $popularLocation['title'] }}
                  </h2>
                </header>
                <img class="ds-card-image"
                     src="{{ $popularLocation->text_pic1 ?? $randomImages[array_rand($randomImages)] }}"
                     alt="{{ $popularLocation->title ?? $popularLocation['title'] }}">
                <footer class="ds-card-footer">
                  <a href="#" class="ds-card-button">
                    see more
                  </a>
                </footer>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <div>
    @include('pages.main.sections.blog')
  </div>
</div>
@endsection



<style>
/* Allgemeine Styles (weitgehend unverändert) */
.explore-section {
    background: linear-gradient(135deg, #e6f0fa 0%, #f9fafb 100%);
    min-height: auto;
    padding-top: 80px;
    padding-bottom: 60px;
}

.text-color-dark {
    color: #1a202c;
    font-weight: 800;
    letter-spacing: -0.5px;
}

.text-color-grey {
    color: #718096;
    font-size: 1.1rem;
}

/* Flexbox Fallback für ältere Firefox-Versionen */
.activity-options, .time-options {
    display: flex;
    justify-content: center;
    gap: 20px;
}

@supports not (gap: 20px) {
    .activity-options > *,
    .time-options > * {
        margin: 0 10px;
    }
}

.activity-card, .time-card {
    position: relative;
    cursor: pointer;
}

.activity-input, .time-input {
    display: none;
}

.activity-content, .time-content {
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 15px;
    padding: 20px;
    width: 140px;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.activity-content i, .time-content i {
    color: #ff6b6b;
    margin-bottom: 10px;
    transition: transform 0.3s ease;
}

.activity-content span, .time-content span {
    display: block;
    color: #2d3748;
    font-weight: 600;
    font-size: 1rem;
}

.activity-input:checked + .activity-content,
.time-input:checked + .time-content {
    background: linear-gradient(135deg, #ff6b6b, #ff8e53);
    border-color: #ff6b6b;
    color: white;
    box-shadow: 0 8px 20px rgba(255, 107, 107, 0.3);
}

.activity-input:checked + .activity-content i,
.time-input:checked + .time-content i,
.activity-input:checked + .activity-content span,
.time-input:checked + .time-content span {
    color: white;
}

.activity-card:hover .activity-content,
.time-card:hover .time-content {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
}

.activity-card:hover .activity-content i,
.time-card:hover .time-content i {
    transform: scale(1.2);
}

.btn-explore {
    background: linear-gradient(135deg, #ff6b6b, #ff8e53);
    border: none;
    padding: 15px 40px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 1.2rem;
    color: white;
    text-transform: uppercase;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
}

.btn-explore:hover {
    background: linear-gradient(135deg, #ff8e53, #ff6b6b);
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.6);
}
</style>




<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
.destination-slider-wrapper {
  --ds-primary: #ff6b6b;
  --ds-secondary: #ff8e53;
  --ds-light: #ffffff;
  --ds-dark: #333333;
  --ds-shadow: 0 10px 20px rgba(0,0,0,0.1);

  contain: content;
  position: relative;
  z-index: 0;
  min-height: auto;
  padding-bottom: 2rem;
}

.destination-slider-section {
  position: relative;
  padding: 1rem 0;
  overflow: hidden;
  background: url("https://images.pexels.com/photos/18394681/pexels-photo-18394681/free-photo-of-puesta-de-sol-silueta-tarde-noche.jpeg")
              center/cover no-repeat;
  min-height: 70vh;
  height: fit-content;
}

.ds-container {
  position: relative;
  z-index: 1;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 20px;

}

.ds-header {
  padding: 40px 0;
  text-align: center;
}

.ds-title {
  color: var(--ds-light);
  font-size: 2.5rem;
  text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
  margin-bottom: 2rem;
  letter-spacing: 1px;
}

.ds-slider {
  padding: 0.5rem 0;
  width: 100%;
}

.ds-card {
  height: auto;
  min-height: 300px;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid rgba(255,255,255,0.2);
  background: rgba(255,255,255,0.1);
  backdrop-filter: blur(5px);
}

.ds-card:hover {
  transform: translateY(-10px);
  box-shadow: var(--ds-shadow);
}

.ds-card-content {
  display: flex;
  flex-direction: column;
}

.ds-card-header {
  padding: 3rem 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-grow: 1;
}

.ds-card-title {
  color: var(--ds-light);
  text-transform: uppercase;
  letter-spacing: 4px;
  font-size: 1.5rem;
  transition: all 0.4s ease;
  text-align: center;
}

.ds-card-image {
  width: 100%;
  aspect-ratio: 16/9;
  object-fit: cover;
  transition: opacity 0.4s ease;
}

.ds-card-footer {
  padding: 40px 25px;
  display: flex;
  justify-content: center;
}

.ds-card-button {
  background: linear-gradient(135deg, var(--ds-primary), var(--ds-secondary));
  border: none;
  padding: 15px 40px;
  border-radius: 50px;
  font-weight: 700;
  font-size: 1rem;
  color: white;
  text-transform: uppercase;
  transition: all 0.3s ease;
  box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
  text-decoration: none;
  cursor: pointer;
  display: inline-block;
  touch-action: manipulation;
}

.ds-card-button:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(255, 107, 107, 0.6);
  background: linear-gradient(135deg, var(--ds-secondary), var(--ds-primary));
}

.swiper-slide {
  transform: scale(0.8);
  transition: transform 0.5s;
  min-height: 450px;
  padding: 10px;
  box-sizing: border-box;
}



.swiper-slide-active {
  transform: scale(1);
}


.swiper-wrapper {
  height: fit-content;
  display: flex;
}

.swiper-slide-active .ds-card {
  background: var(--ds-light);
}

.swiper-slide-active .ds-card-title {
  color: var(--ds-dark);
}

.swiper-slide:not(.swiper-slide-active) .ds-card-image,
.swiper-slide:not(.swiper-slide-active) .ds-card-footer {
  opacity: 0;
  pointer-events: none;
}

/* Responsive Anpassungen */
@media (max-width: 1200px) {
  .destination-slider-section {
    min-height: 65vh;
  }
  .ds-container {
    padding: 0 15px;
  }
  .ds-title {
    font-size: 2.2rem;
  }
  .ds-card {
    min-height: 280px;
  }
  .ds-card-title {
    font-size: 1.4rem;
    letter-spacing: 3px;
  }
  .ds-card-header {
    padding: 2rem 1.5rem;
  }
  .ds-card-footer {
    padding: 30px 20px;
  }
  .ds-card-button {
    padding: 12px 35px;
    font-size: 0.95rem;
  }
  .swiper-slide {
    min-height: 420px;
  }
}

@media (max-width: 768px) {
  .destination-slider-section {
    min-height: 55vh;
  }
  .ds-title {
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
  }
  .ds-header {
    padding: 30px 0;
  }
  .ds-card {
    min-height: 260px;
  }
  .ds-card-title {
    font-size: 1.2rem;
    letter-spacing: 2px;
  }
  .ds-card-header {
    padding: 1.5rem 1rem;
  }
  .ds-card-image {
    aspect-ratio: 4/3; /* Bessere Proportionen für kleinere Bildschirme */
  }
  .ds-card-footer {
    padding: 20px 15px;
  }
  .ds-card-button {
    padding: 10px 30px;
    font-size: 0.9rem;
  }
  .swiper-slide {
    min-height: 380px;
    padding: 5px;
  }
  .swiper-slide-active {
    transform: scale(0.95); /* Etwas kleinere Skalierung für bessere Sichtbarkeit */
  }
  .swiper-slide {
    transform: scale(0.75); /* Inaktive Slides etwas kleiner */
  }
}

@media (max-width: 480px) {
  .destination-slider-section {
    min-height: 45vh;
  }
  .ds-title {
    font-size: 1.5rem;
    margin-bottom: 1rem;
  }
  .ds-header {
    padding: 20px 0;
  }
  .ds-card {
    min-height: 240px;
  }
  .ds-card-title {
    font-size: 1rem;
    letter-spacing: 1px;
  }
  .ds-card-header {
    padding: 1rem 0.5rem;
  }
  .ds-card-image {
    aspect-ratio: 3/2; /* Noch kompaktere Proportionen für sehr kleine Bildschirme */
  }
  .ds-card-footer {
    padding: 15px 10px;
  }
  .ds-card-button {
    padding: 8px 20px;
    font-size: 0.8rem;
  }
  .swiper-slide {
    min-height: 340px;
    padding: 3px;
  }
  .swiper-slide-active {
    transform: scale(0.9);
  }
  .swiper-slide {
    transform: scale(0.7);
  }
}

@media (max-width: 360px) {
  .destination-slider-section {
    min-height: 40vh;
  }
  .ds-title {
    font-size: 1.3rem;
  }
  .ds-card {
    min-height: 220px;
  }
  .ds-card-title {
    font-size: 0.9rem;
  }
  .ds-card-header {
    padding: 0.8rem 0.3rem;
  }
  .ds-card-footer {
    padding: 10px 5px;
  }
  .ds-card-button {
    padding: 6px 15px;
    font-size: 0.75rem;
  }
  .swiper-slide {
    min-height: 300px;
  }
}
</style>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  new Swiper('#destinationSlider', {
    slidesPerView: 5,
    slidesPerGroup: 1,
    centeredSlides: true,
    loop: true,
    autoplay: {
      delay: 3000,
      pauseOnMouseEnter: true,
    },
    spaceBetween: 5,
    breakpoints: {
      1200: {
        slidesPerView: 3,
        spaceBetween: 5
      },
      768: {
        slidesPerView: 2,
        spaceBetween: 5,
        centeredSlides: true,
      },
      480: {
        slidesPerView: 1,
        spaceBetween: 3,
        centeredSlides: true,
      },
      360: {
        slidesPerView: 1,
        spaceBetween: 2,
        centeredSlides: true,
      }
    }
  });
});
</script>
