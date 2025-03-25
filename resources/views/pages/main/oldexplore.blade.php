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

<!-- Splide Slider für meistbesuchte Reiseziele -->
<div class="mt-5 popular-locations">
    <h2 class="text-center text-color-dark mb-4 animate__animated animate__fadeIn">
        @autotranslate('Meistbesuchte Reiseziele', app()->getLocale())
    </h2>
    <div class="splide" id="splide-slider">
        <div class="splide__track">
            <ul class="splide__list">
                @php
                    $carouselItems = $popularLocations->count() < 3 ? $popularLocations->concat($popularLocations)->take(10) : $popularLocations;
                    $randomImages = [
                        asset('img/locations/woman-meditating-beach-with-copy-space.jpg'),
                        asset('img/locations/medium-shot-man-exploring-with-map.jpg'),
                        asset('img/locations/beach-area-blurred-night.jpg'),
                    ];
                @endphp
                @foreach ($carouselItems as $popularLocation)
                    <li class="splide__slide">
                        <div class="slider-card-wrapper">
                            <div class="slider-card">
                                <div class="slider-card-image">
                                    <img src="{{ $popularLocation->text_pic1 ?? $randomImages[array_rand($randomImages)] }}"
                                         alt="{{ $popularLocation->title }}">
                                </div>
                                <div class="slider-card-content">
                                    <h5 class="slider-card-title">{{ $popularLocation->title }}</h5>
                                    <p class="slider-card-text">
                                        <i class="fas fa-search"></i> {{ $popularLocation->search_count }} Suchen
                                    </p>
                                    <a href="{{ url('/details/' . ($popularLocation->continent_alias ?? 'unknown') . '/' . ($popularLocation->country_alias ?? 'unknown') . '/' . ($popularLocation->alias ?? 'unknown')) }}"
                                       class="slider-card-button">
                                        @autotranslate('Details', app()->getLocale())
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>



        </div>
    </section>
@endsection

{{-- Splide CSS und JS einbinden --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@latest/dist/css/splide.min.css">

<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@latest/dist/js/splide.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Splide('#splide-slider', {
        type: 'loop',
        perPage: 5,
        gap: '15px',
        pagination: false,
        autoplay: true,
        interval: 3000,
        pauseOnHover: true,
        easing: 'cubic-bezier(0.25, 1, 0.5, 1)',
        arrows: true,
        focus: 'center',
        height: 'auto', // Dynamische Höhe
        breakpoints: {
            1024: { perPage: 3 },
            768: { perPage: 2 },
            576: { perPage: 1 }
        },
        classes: {
            arrows: 'splide__arrows custom-arrows',
            arrow: 'splide__arrow custom-arrow',
            prev: 'splide__arrow--prev custom-prev',
            next: 'splide__arrow--next custom-next',
            pagination: 'splide__pagination custom-pagination',
            page: 'splide__pagination__page custom-page'
        }
    }).mount();

    const cards = document.querySelectorAll('.activity-card, .time-card');
    cards.forEach(card => {
        card.addEventListener('click', () => {
            card.querySelector('input').checked = true;
        });
    });
});
</script>

<style>
/* Allgemeine Styles (weitgehend unverändert) */
.explore-section {
    background: linear-gradient(135deg, #e6f0fa 0%, #f9fafb 100%);
    min-height: 100vh;
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

/* Slider Styles */
.popular-locations {
    padding-bottom: 40px;
}

.splide {
    position: relative;
    height: auto; /* Dynamische Höhe */
    min-height: 300px; /* Mindesthöhe für Firefox */
}

.splide__track {
    padding: 10px 0;
    height: auto;
}

.splide__slide {
    height: auto;
}

.slider-card-wrapper {
    padding: 0 10px;
    height: auto;
}

.slider-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: fit-content;
    min-height: 300px; /* Mindesthöhe für Karten */
    display: flex;
    flex-direction: column;
}

.slider-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 8px 25px rgba(255, 107, 107, 0.3);
}

.slider-card-image {
    position: relative;
    width: 100%;
    height: 180px;
    overflow: hidden;
    flex-shrink: 0; /* Verhindert Schrumpfen */
}

.slider-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease, filter 0.3s ease;
}

.slider-card:hover .slider-card-image img {
    transform: scale(1.05);
    filter: brightness(1.1);
}

.slider-card-image::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.4);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.slider-card:hover .slider-card-image::after {
    opacity: 1;
}

.slider-card-content {
    padding: 15px;
    background: white;
    position: relative;
    z-index: 1;
    flex-grow: 1; /* Füllt den restlichen Raum */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.slider-card-title {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 8px;
    color: #2d3748;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.slider-card-text {
    font-size: 0.85rem;
    color: #718096;
    margin-bottom: 12px;
}

.slider-card-button {
    display: inline-block;
    padding: 6px 12px;
    background: linear-gradient(135deg, #ff6b6b, #ff8e53);
    color: white;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    align-self: flex-start; /* Button bleibt unten links */
}

.slider-card-button:hover {
    background: linear-gradient(135deg, #ff8e53, #ff6b6b);
    box-shadow: 0 3px 10px rgba(255, 107, 107, 0.3);
}

/* Pfeile und Pagination */
.custom-arrows {
    position: absolute;
    top: 90px; /* Zentriert auf der Höhe des Bildes (180px / 2) */
    width: 100%;
    display: flex;
    justify-content: space-between;
    pointer-events: none;
}

.custom-arrow {
    background: #ff6b6b;
    border-radius: 50%;
    width: 2.5rem;
    height: 2.5rem;
    opacity: 0.8;
    transition: opacity 0.3s ease;
    pointer-events: auto;
}

.custom-arrow:hover {
    opacity: 1;
}

.custom-prev {
    left: -3rem;
}

.custom-next {
    right: -3rem;
}

.custom-pagination {
    position: absolute;
    bottom: -1em;
}

.custom-page {
    width: 12px;
    height: 12px;
    margin: 0 5px;
    background: #cbd5e0;
    transition: transform 0.3s ease;
}

.custom-page.is-active {
    background: #ff6b6b;
    transform: scale(1.3);
}

/* Responsivität */
@media (max-width: 768px) {
    .slider-card {
        min-height: 250px;
    }
    .slider-card-image {
        height: 150px;
    }
    .slider-card-content {
        padding: 12px;
    }
    .slider-card-title {
        font-size: 0.9rem;
    }
    .slider-card-text {
        font-size: 0.75rem;
    }
    .slider-card-button {
        padding: 5px 10px;
        font-size: 0.7rem;
    }
    .custom-prev {
        left: -2rem;
    }
    .custom-next {
        right: -2rem;
    }
    .custom-arrows {
        top: 75px; /* Zentriert auf der Höhe des Bildes (150px / 2) */
    }
}
</style>
