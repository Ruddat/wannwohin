{{-- resources\views\pages\main\explore-results.blade.php --}}

@extends('layouts.main')

@section('content')
    <section class="explore-results-section py-5">
        <div class="container">
            <!-- Schöne Überschrift -->
            <h1 class="section-title text-center text-color-dark mb-3">
                @autotranslate('Deine Reisevorschläge', app()->getLocale())
            </h1>
            <p class="text-center text-color-grey mb-5">
                @autotranslate("Hier sind die besten Ziele für {$activity} zu deinem gewählten Zeitpunkt!", app()->getLocale())
            </p>

            <!-- Filter-Anzeige -->
            <div class="filter-bar d-flex justify-content-center align-items-center mb-5">
                <span class="filter-label me-2">@autotranslate('Deine Auswahl:', app()->getLocale())</span>
                <span class="badge bg-primary me-2">{{ ucfirst($activity) }}</span>
                <span class="badge bg-secondary">{{ ucfirst($time) }}</span>
                <a href="{{ route('explore') }}" class="btn btn-sm btn-outline-primary ms-3">
                    @autotranslate('Filter ändern', app()->getLocale())
                </a>
            </div>

            <!-- Locations Grid -->
            <div class="row g-4" data-aos="fade-up">
                @forelse ($locations as $location)
                    <div class="col-md-4 col-lg-3">
                        <div class="card h-100 shadow-sm hover-card">
                            <div class="card-img-wrapper">
                                <img src="{{ $location->text_pic1 ?? asset('img/locations/default.jpg') }}"
                                     class="card-img-top lazyload"
                                     alt="{{ $location->title }}"
                                     data-src="{{ $location->text_pic1 ?? asset('img/locations/default.jpg') }}">
                                <div class="card-img-overlay">
                                    <button class="btn btn-favorite" data-location-id="{{ $location->id }}">
                                        <i class="fas fa-heart {{ $location->is_favorite ?? false ? 'text-danger' : 'text-white' }}"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $location->title }}</h5>
                                <p class="card-text">
                                    <i class="fas fa-globe me-1"></i>
                                    {{ $location->country_alias ?? $location->country ?? 'Unbekannt' }}
                                </p>
                                @if (isset($location->distance))
                                    <p class="card-text">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ round($location->distance, 1) }} km entfernt
                                    </p>
                                @endif
                                <p class="card-text">
                                    <i class="fas fa-thermometer-half me-1"></i>
                                    {{ $location->climate_details_lnam ?? 'N/A' }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ url('/details/' . ($location->continent_alias ?? 'unknown') . '/' . ($location->country_alias ?? 'unknown') . '/' . ($location->alias ?? 'unknown')) }}"
                                       class="btn btn-primary btn-sm">
                                        @autotranslate('Mehr erfahren', app()->getLocale())
                                    </a>
                                    <small class="text-muted">
                                        <i class="fas fa-star text-warning"></i> {{ $location->rating ?? '4.5' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-color-grey">@autotranslate('Keine passenden Ziele gefunden. Versuche es erneut!', app()->getLocale())</p>
                        <a href="{{ route('explore') }}" class="btn btn-outline-primary">
                            @autotranslate('Neue Suche', app()->getLocale())
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Zurück-Button -->
            <div class="text-center mt-5">
                <a href="{{ route('explore') }}" class="btn btn-outline-secondary">
                    @autotranslate('Zurück zu den Fragen', app()->getLocale())
                </a>
            </div>
        </div>
    </section>
@endsection

@section('styles')
    <style scoped>
        .explore-results-section { background: #f9fafb; min-height: 100vh; padding-top: 6rem; }

        /* Überschrift */
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            position: relative;
            display: inline-block;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 4px;
            background: #3182ce;
            border-radius: 2px;
        }

        /* Cards */
        .card { border: none; border-radius: 15px; overflow: hidden; transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card-img-wrapper {
            position: relative;
            width: 100%;
            height: 200px; /* Feste Höhe für alle Bilder */
            overflow: hidden;
        }
        .card-img-top {
            width: 100%; /* Breite auf Container beschränken */
            height: 100%; /* Höhe auf Container beschränken */
            object-fit: cover; /* Bild wird zugeschnitten, keine Verzerrung */
            object-position: center; /* Bild mittig ausrichten */
            transition: opacity 0.3s ease;
        }
        .lazyload { opacity: 0; }
        .lazyloaded { opacity: 1; }
        .card-img-overlay { position: absolute; top: 10px; right: 10px; }
        .btn-favorite { background: rgba(0, 0, 0, 0.6); border: none; padding: 5px; border-radius: 50%; }
        .btn-favorite:hover { background: rgba(0, 0, 0, 0.8); }
        .card-body { padding: 1.5rem; }
        .card-title { color: #2d3748; font-weight: 600; font-size: 1.2rem; }
        .card-text { color: #4a5568; font-size: 0.9rem; margin-bottom: 0.5rem; }
        .hover-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1); }

        /* Buttons */
        .btn-primary { background: #3182ce; border: none; padding: 0.5rem 1rem; }
        .btn-primary:hover { background: #2b6cb0; }
        .btn-outline-secondary { border-color: #3182ce; color: #3182ce; padding: 0.5rem 1.5rem; }
        .btn-outline-secondary:hover { background: #3182ce; color: white; }

        /* Filter Bar */
        .filter-bar { background: #fff; padding: 1rem; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05); }
        .filter-label { font-weight: 500; color: #4a5568; }
        .badge { font-size: 0.9rem; padding: 0.5rem 1rem; }
    </style>
@endsection


    <!-- LazyLoad für Bilder -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js" async></script>

