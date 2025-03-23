{{-- resources\views\pages\main\explore.blade.php --}}
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

            <!-- Karussell bleibt unverändert -->
            <div class="mt-5 popular-locations">
                <h2 class="text-center text-color-dark mb-4 animate__animated animate__fadeIn">
                    @autotranslate('Meistbesuchte Reiseziele', app()->getLocale())
                </h2>
                <div id="popularLocationsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000" data-bs-wrap="true">
                    <div class="carousel-inner">
                        @php
                            $carouselItems = $popularLocations->count() < 3 ? $popularLocations->concat($popularLocations)->take(10) : $popularLocations;
                            $randomImages = [
                                asset('img/locations/festive-red-santa-hat.jpg'),
                                'https://via.placeholder.com/400x250?text=Reiseziel+2',
                                'https://via.placeholder.com/400x250?text=Reiseziel+3',
                            ];
                        @endphp
                        @foreach ($carouselItems->chunk(3) as $chunkIndex => $chunk)
                            <div class="carousel-item {{ $chunkIndex == 0 ? 'active' : '' }}">
                                <div class="row g-4 justify-content-center">
                                    @foreach ($chunk as $popularLocation)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="card h-100 shadow-lg hover-card position-relative overflow-hidden">
                                                <img src="{{ $popularLocation->text_pic1 ?? $randomImages[array_rand($randomImages)] }}"
                                                     class="card-img-top" alt="{{ $popularLocation->title }}">
                                                <div class="card-overlay">
                                                    <h5 class="card-title text-white mb-2">{{ $popularLocation->title }}</h5>
                                                    <p class="card-text text-white">
                                                        <i class="fas fa-search"></i> {{ $popularLocation->search_count }} Suchen
                                                    </p>
                                                    <a href="{{ url('/details/' . ($popularLocation->continent_alias ?? 'unknown') . '/' . ($popularLocation->country_alias ?? 'unknown') . '/' . ($popularLocation->alias ?? 'unknown')) }}"
                                                       class="btn btn-outline-light btn-sm">
                                                        @autotranslate('Details', app()->getLocale())
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#popularLocationsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#popularLocationsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    <div class="carousel-indicators">
                        @foreach ($carouselItems->chunk(3) as $chunkIndex => $chunk)
                            <button type="button" data-bs-target="#popularLocationsCarousel" data-bs-slide-to="{{ $chunkIndex }}"
                                    class="{{ $chunkIndex == 0 ? 'active' : '' }}" aria-label="Slide {{ $chunkIndex + 1 }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

<style scoped>
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

/* Aktivitäts- und Zeitkarten */
.activity-options, .time-options {
    display: flex;
    justify-content: center;
    gap: 20px;
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

/* Button */
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

/* Karussell-Styling bleibt weitgehend gleich */
.popular-locations {
    padding-bottom: 40px;
}

.card {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.card-img-top {
    height: 250px;
    object-fit: cover;
    border: 2px solid white;
    border-radius: 18px;
    transition: transform 0.4s ease-in-out;
}

.card-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.85), transparent);
    padding: 20px;
    color: white;
    transform: translateY(100%);
    transition: transform 0.4s ease-in-out;
}

.card:hover .card-overlay {
    transform: translateY(0);
}

.card:hover .card-img-top {
    transform: scale(1.08);
}

.carousel-control-prev-icon, .carousel-control-next-icon {
    filter: invert(100%);
    width: 30px;
    height: 30px;
}

.carousel-indicators button {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background-color: #3182ce;
    border: none;
    margin: 0 5px;
}

.carousel-indicators .active {
    background-color: #ff6b6b;
}

/* Responsive Anpassungen */
@media (max-width: 768px) {
    .activity-content, .time-content {
        width: 120px;
        padding: 15px;
    }
    .btn-explore {
        padding: 12px 30px;
        font-size: 1rem;
    }
    .card-img-top {
        height: 200px;
    }
}
</style>

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.activity-card, .time-card');
            cards.forEach(card => {
                card.addEventListener('click', () => {
                    card.querySelector('input').checked = true;
                });
            });
        });
    </script>
@endsection
   <!-- tabler icons-->
   <link rel="stylesheet" type="text/css" href="{{ asset('/assets/ra-admin/vendor/tabler-icons/tabler-icons.css') }}">
   <!-- Tabler icons -->
   <script src="{{ asset('/assets/js/tabler-icons.js') }}"></script>

