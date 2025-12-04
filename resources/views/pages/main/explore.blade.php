@extends('layouts.main')

@section('content')

<!-- Explore Section -->
<section class="explore-section py-5">
    <div class="container">
        <h1 class="text-center text-color-dark mb-4 animate__animated animate__fadeIn">
            @autotranslate('Finde dein Abenteuer!', app()->getLocale())
        </h1>
        <p class="text-center text-color-grey mb-5 animate__animated animate__fadeIn animate__delay-1s">
            @autotranslate('Beantworte diese Fragen, und wir schlagen dir spontan ein Ziel vor!', app()->getLocale())
        </p>

        <form action="{{ route('explore.results') }}" method="GET" class="row g-4 justify-content-center">
<!-- Aktivität -->
<div class="col-md-10">
    <label class="form-label fw-bold text-center d-block mb-3 animate__animated animate__fadeInUp">
        @autotranslate('Was möchtest du erleben?', app()->getLocale())
    </label>
    <div class="activity-options d-flex flex-wrap justify-content-center gap-3">
        @foreach([
            ['value' => 'relax', 'icon' => 'fas fa-umbrella-beach', 'text' => 'Entspannen'],
            ['value' => 'adventure', 'icon' => 'fas fa-hiking', 'text' => 'Abenteuer'],
            ['value' => 'culture', 'icon' => 'fas fa-landmark', 'text' => 'Kultur'],
            ['value' => 'amusement', 'icon' => 'ti ti-rollercoaster', 'text' => 'Freizeitparks', 'style' => 'font-size: 2.5rem;'],
        ] as $option)
            <label class="activity-card">
                <input type="radio" name="activity" value="{{ $option['value'] }}" class="activity-input">
                <div class="activity-content">
                    <i class="{{ $option['icon'] }} fa-2x" @if(isset($option['style'])) style="{{ $option['style'] }}" @endif></i>
                    <span>@autotranslate($option['text'], app()->getLocale())</span>
                </div>
            </label>
        @endforeach
    </div>
</div>

<!-- Zeitpunkt -->
<div class="col-md-10 mt-5">
    <label class="form-label fw-bold text-center d-block mb-3 animate__animated animate__fadeInUp">
        @autotranslate('Wann möchtest du reisen?', app()->getLocale())
    </label>
    <div class="time-options d-flex flex-wrap justify-content-center gap-3">
        @foreach([
            ['value' => 'now', 'icon' => 'fas fa-clock', 'text' => 'Jetzt'],
            ['value' => 'month', 'icon' => 'fas fa-calendar-alt', 'text' => 'Nächster Monat'],
            ['value' => 'later', 'icon' => 'fas fa-hourglass-half', 'text' => 'Später'],
        ] as $option)
            <label class="time-card">
                <input type="radio" name="time" value="{{ $option['value'] }}" class="time-input">
                <div class="time-content">
                    <i class="{{ $option['icon'] }} fa-2x"></i>
                    <span>@autotranslate($option['text'], app()->getLocale())</span>
                </div>
            </label>
        @endforeach
    </div>
</div>

            @if ($latitude && $longitude)
                <input type="hidden" name="lat" value="{{ $latitude }}">
                <input type="hidden" name="lon" value="{{ $longitude }}">
            @endif


            <div class="col-md-10 text-center mt-3">
                <div id="error-message" class="error-message-cool" style="display: none;"></div>
            </div>

            <div class="col-md-10 text-center mt-5">
                <button type="submit" id="explore-submit" class="btn btn-explore animate__animated animate__pulse animate__infinite">
                    @autotranslate('Ergebnisse anzeigen', app()->getLocale())
                </button>
            </div>


        </form>
    </div>
</section>


<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Skript geladen!');
    const form = document.querySelector('form');
    const submitButton = document.getElementById('explore-submit');
    const errorMessage = document.getElementById('error-message');

    if (!form || !submitButton || !errorMessage) {
        console.error('Formular, Button oder Fehlermeldungs-Element nicht gefunden!');
        return;
    }

    submitButton.addEventListener('click', function(event) {
        console.log('Button geklickt!');
        event.preventDefault();

        const activitySelected = document.querySelector('input[name="activity"]:checked');
        const timeSelected = document.querySelector('input[name="time"]:checked');

        if (!activitySelected || !timeSelected) {
            console.log('Validierung fehlgeschlagen – mindestens eine Option fehlt');
            errorMessage.style.display = 'block';
            errorMessage.classList.remove('fade-out');
            errorMessage.classList.add('fade-in');

            const messages = [
                "Whoops! Ohne beide Antworten beamen wir dich ins Chaos-Universum! 🚀",
                "Hey, wir brauchen zwei Puzzleteile – nicht nur eins, du Schlaumeier! 🧩",
                "Äh, halb ausgefüllt? Das ist, als würdest du nur mit einem Schuh reisen! 👟",
                "Zwei Antworten, bitte! Sonst landet dein Abenteuer im Bermuda-Dreieck! 🌊",
                "Klick, klick! Beides auswählen, oder wir schicken dich zur Spaßstrafe! 😜",
                "Keine halben Sachen! Sonst wird dein Urlaub ein Rätsel ohne Lösung! ❓",
                "Hallo? Zwei Felder, zwei Klicks – oder willst du im Nichts urlauben? 🌌"
            ];
            const randomMessage = messages[Math.floor(Math.random() * messages.length)];
            errorMessage.innerHTML = randomMessage;
            console.log('Fehlermeldung gesetzt:', randomMessage);

            setTimeout(() => {
                errorMessage.classList.remove('fade-in');
                errorMessage.classList.add('fade-out');
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                    errorMessage.classList.remove('fade-out');
                }, 500);
            }, 5000);
        } else {
            console.log('Validierung erfolgreich – Weiterleitung wird vorbereitet');
            errorMessage.style.display = 'none';

            // Hole die ausgewählten Werte
            const activity = activitySelected.value;
            const time = timeSelected.value;

            // Basis-URL direkt aus der Route
            const baseUrl = "{{ route('explore.results') }}"; // Wird zu /explore/results gerendert
            let queryParams = `activity=${encodeURIComponent(activity)}&time=${encodeURIComponent(time)}`;

            // Prüfe, ob Koordinaten im Formular vorhanden sind
            const latInput = form.querySelector('input[name="lat"]');
            const lonInput = form.querySelector('input[name="lon"]');
            if (latInput && lonInput) {
                const lat = latInput.value;
                const lon = lonInput.value;
                queryParams += `&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lon)}`;
            }

            // Baue die vollständige URL
            const redirectUrl = `${baseUrl}?${queryParams}`;
            console.log('Weiterleitung zu:', redirectUrl);

            // Leite zur neuen URL weiter
            window.location.href = redirectUrl;
        }
    });
});
</script>


<!-- Explore Section CSS -->
<style>
.explore-section {
    background: linear-gradient(135deg, #e6f0fa 0%, #f9fafb 100%);
    padding: 80px 0 60px;
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

.activity-options, .time-options {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}

.error-message-cool {
    background: linear-gradient(135deg, #ff6b6b, #ff8e53);
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
    padding: 15px 20px;
    border-radius: 12px;
    max-width: 500px;
    margin: 0 auto;
    box-shadow: 0 5px 15px rgba(255, 107, 107, 0.5);
    border: 2px solid #fff;
    text-align: center;
    position: relative;
    overflow: hidden;
}

/* Kleiner Glitzereffekt */
.error-message-cool::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: rgba(255, 255, 255, 0.2);
    transform: rotate(30deg);
    animation: shine 2s infinite;
}

/* Fade-In Animation */
.fade-in {
    animation: fadeIn 0.5s ease-in;
}

/* Fade-Out Animation */
.fade-out {
    animation: fadeOut 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
        transform: translateY(0);
    }
    to {
        opacity: 0;
        transform: translateY(10px);
    }
}

@keyframes shine {
    0% {
        transform: translateX(-100%) rotate(30deg);
    }
    50% {
        transform: translateX(100%) rotate(30deg);
    }
    100% {
        transform: translateX(-100%) rotate(30deg);
    }
}


@supports not (gap: 20px) {
    .activity-options > *, .time-options > * {
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
.activity-input:checked + .activity-content span,
.time-input:checked + .time-content i,
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

<!-- Destination Slider Section -->
<div class="destination-slider-wrapper">
    <section class="destination-slider-section">
        <div class="ds-container">
            <header class="ds-header">
                <h1 class="ds-title">
                    @autotranslate('Meistbesuchte Reiseziele', app()->getLocale())
                </h1>
            </header>

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
                                            <span class="ds-card-location">
                                                {{ $popularLocation->country_alias ?? 'Unbekanntes Land' }},
                                                {{ $popularLocation->continent_alias ?? 'Unbekannter Kontinent' }}
                                            </span>
                                        </h2>
                                    </header>
                                    <img class="ds-card-image"
                                         src="{{ $popularLocation->text_pic1 ?? $randomImages[array_rand($randomImages)] }}"
                                         alt="{{ $popularLocation->title ?? $popularLocation['title'] }}">
                                    <div class="ds-card-details">
                                        <p><strong>Beliebtheit:</strong> {{ $popularLocation->search_count ?? 0 }} Suchen</p>
                                        <p><strong>Klima:</strong> {{ $popularLocation->climate_details_lnam ?? 'Keine Info' }}</p>
                                        @php
                                            $activities = [];
                                            if ($popularLocation->list_beach) $activities[] = 'Strand';
                                            if ($popularLocation->list_citytravel) $activities[] = 'Städtereise';
                                            if ($popularLocation->list_sports) $activities[] = 'Sport';
                                            if ($popularLocation->list_nature) $activities[] = 'Natur';
                                            if ($popularLocation->list_culture) $activities[] = 'Kultur';
                                            $activityList = implode(', ', $activities);
                                        @endphp
                                        <p><strong>Aktivitäten:</strong> {{ $activityList ?: 'Keine spezifizierten Aktivitäten' }}</p>
                                    </div>
                                    <footer class="ds-card-footer">
                                        <a href="{{ url('/details/' . ($popularLocation->continent_alias ?? 'unknown') . '/' . ($popularLocation->country_alias ?? 'unknown') . '/' . ($popularLocation->alias ?? 'unknown')) }}"
                                           class="ds-card-button">
                                            Mehr sehen
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
        {{--
    <div>

        @include('pages.main.sections.blog')

    </div>
--}}
</div>

<!-- Slider Dependencies -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<!-- Slider CSS -->
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
    padding-bottom: 2rem;
}

.destination-slider-section {
    position: relative;
    padding: 1rem 0;
    overflow: hidden;
    background: url("https://images.pexels.com/photos/18394681/pexels-photo-18394681/free-photo-of-puesta-de-sol-silueta-tarde-noche.jpeg") center/cover no-repeat;
    min-height: 70vh;
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
    perspective: 1000px;
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
    position: relative;
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
    text-align: center;
    margin-bottom: 0;
    position: relative;
    z-index: 2;
    transition: transform 0.5s ease;
}

.ds-card-location {
    display: block;
    font-size: 1rem;
    text-transform: capitalize;
    color: #bbb;
    letter-spacing: 1px;
    margin-top: 5px;
    transition: transform 0.5s ease;
}

.ds-card-image {
    width: 100%;
    aspect-ratio: 16/9;
    object-fit: cover;
    transition: transform 0.5s ease, opacity 0.5s ease; /* Übergang für Sichtbarkeit */
    position: relative;
    z-index: 1;
    opacity: 0; /* Standardmäßig unsichtbar */
}

.swiper-slide-active .ds-card-image {
    transform: translateZ(20px) scale(1.05);
    opacity: 1; /* Nur auf aktivem Slide sichtbar */
}

.ds-card-details {
    padding: 15px 20px;
    color: var(--ds-light);
    font-size: 0.9rem;
    text-align: center;
    background: rgba(255, 255, 255, 0.05);
    position: relative;
    z-index: 2;
    transition: transform 0.5s ease;
}

.ds-card-details p {
    margin: 5px 0;
}

.ds-card-details strong {
    color: var(--ds-primary);
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
    display: inline-block;
}

.ds-card-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(255, 107, 107, 0.6);
    background: linear-gradient(135deg, var(--ds-secondary), var(--ds-primary));
}

.swiper-slide {
    min-height: 500px;
    padding: 10px;
    box-sizing: border-box;
    transform: scale(0.8) rotateY(15deg);
    transition: transform 0.5s ease, rotate 0.5s ease;
}

.swiper-slide-active {
    transform: scale(1) rotateY(0deg);
}

.swiper-slide-active .ds-card {
    background: var(--ds-light);
}

.swiper-slide-active .ds-card-title {
    color: var(--ds-dark);
    transform: translateZ(30px);
}

.swiper-slide-active .ds-card-location {
    color: #666;
    transform: translateZ(25px);
}

.swiper-slide-active .ds-card-details {
    color: var(--ds-dark);
    transform: translateZ(15px);
}

.swiper-slide-active .ds-card-details strong {
    color: var(--ds-secondary);
}

.swiper-slide:not(.swiper-slide-active) .ds-card-details,
.swiper-slide:not(.swiper-slide-active) .ds-card-footer {
    display: none;
}

.ds-card:hover .ds-card-image {
    transform: translateZ(30px) scale(1.1);
    opacity: 1; /* Sichtbar bei Hover, aber nur wenn aktiv */
}

.ds-card:hover .ds-card-title {
    transform: translateZ(40px);
}

.ds-card:hover .ds-card-location {
    transform: translateZ(35px);
}

.ds-card:hover .ds-card-details {
    transform: translateZ(20px);
}

/* Responsive Anpassungen */
@media (max-width: 1200px) {
    .destination-slider-section { min-height: 65vh; }
    .ds-container { padding: 0 15px; }
    .ds-title { font-size: 2.2rem; }
    .ds-card { min-height: 280px; }
    .ds-card-title { font-size: 1.4rem; letter-spacing: 3px; }
    .ds-card-header { padding: 2rem 1.5rem; }
    .ds-card-footer { padding: 30px 20px; }
    .ds-card-button { padding: 12px 35px; font-size: 0.95rem; }
    .swiper-slide { min-height: 420px; }
}

@media (max-width: 768px) {
    .destination-slider-section { min-height: 55vh; }
    .ds-title { font-size: 1.8rem; margin-bottom: 1.5rem; }
    .ds-header { padding: 30px 0; }
    .ds-card { min-height: 260px; }
    .ds-card-title { font-size: 1.2rem; letter-spacing: 2px; }
    .ds-card-header { padding: 1.5rem 1rem; }
    .ds-card-image { aspect-ratio: 4/3; }
    .ds-card-footer { padding: 20px 15px; }
    .ds-card-button { padding: 10px 30px; font-size: 0.9rem; }
    .swiper-slide { min-height: 380px; padding: 5px; }
    .swiper-slide-active { transform: scale(0.95); }
    .swiper-slide { transform: scale(0.75) rotateY(15deg); }
}

@media (max-width: 480px) {
    .destination-slider-section { min-height: 45vh; }
    .ds-title { font-size: 1.5rem; margin-bottom: 1rem; }
    .ds-header { padding: 20px 0; }
    .ds-card { min-height: 240px; }
    .ds-card-title { font-size: 1rem; letter-spacing: 1px; }
    .ds-card-header { padding: 1rem 0.5rem; }
    .ds-card-image { aspect-ratio: 3/2; }
    .ds-card-footer { padding: 15px 10px; }
    .ds-card-button { padding: 8px 20px; font-size: 0.8rem; }
    .swiper-slide { min-height: 340px; padding: 3px; }
    .swiper-slide-active { transform: scale(0.9); }
    .swiper-slide { transform: scale(0.7) rotateY(15deg); }
}

@media (max-width: 360px) {
    .destination-slider-section { min-height: 40vh; }
    .ds-title { font-size: 1.3rem; }
    .ds-card { min-height: 220px; }
    .ds-card-title { font-size: 0.9rem; }
    .ds-card-header { padding: 0.8rem 0.3rem; }
    .ds-card-footer { padding: 10px 5px; }
    .ds-card-button { padding: 6px 15px; font-size: 0.75rem; }
    .swiper-slide { min-height: 300px; }
}
</style>

<!-- Slider JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('#destinationSlider', {
        slidesPerView: 5,
        slidesPerGroup: 1,
        limitRotation: true,
        centeredSlides: true,
        loop: true,
        autoplay: {
            delay: 3000,
            pauseOnMouseEnter: true,
        },
        spaceBetween: 5,
        breakpoints: {
            1200: { slidesPerView: 3, spaceBetween: 5 },
            768: { slidesPerView: 2, spaceBetween: 5, centeredSlides: true },
            480: { slidesPerView: 1, spaceBetween: 3, centeredSlides: true },
            360: { slidesPerView: 1, spaceBetween: 2, centeredSlides: true }
        }
    });
});
</script>

@endsection
