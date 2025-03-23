@extends('layouts.main')

@section('content')
    <section class="explore-results-section py-5">
        <div class="container">
            <h1 class="text-center text-color-dark mb-4">
                @autotranslate('Deine Reisevorschläge', app()->getLocale())
            </h1>
            <p class="text-center text-color-grey mb-5">
                @autotranslate("Hier sind die besten Ziele für {$activity} zu deinem gewählten Zeitpunkt!", app()->getLocale())
            </p>

            <div class="row g-4">
                @forelse ($locations as $location)
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm hover-card">
                            <img src="{{ $location->text_pic1 ?? asset('img/locations/default.jpg') }}"
                                 class="card-img-top" alt="{{ $location->title }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $location->title }}</h5>
                                <p class="card-text">
                                    <i class="fas fa-globe"></i> {{ $location->country_alias ?? $location->country ?? 'Unbekannt' }}
                                </p>
                                @if (isset($location->distance))
                                    <p class="card-text">
                                        <i class="fas fa-map-marker-alt"></i> {{ round($location->distance, 1) }} km entfernt
                                    </p>
                                @endif
                                <p class="card-text">
                                    <i class="fas fa-thermometer-half"></i> {{ $location->climate_details_lnam ?? 'N/A' }}
                                </p>
                                <a href="{{ url('/details/' . ($location->continent_alias ?? 'unknown') . '/' . ($location->country_alias ?? 'unknown') . '/' . ($location->alias ?? 'unknown')) }}"
                                   class="btn btn-primary btn-sm">
                                    @autotranslate('Mehr erfahren', app()->getLocale())
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p class="text-color-grey">@autotranslate('Keine passenden Ziele gefunden. Versuche es erneut!', app()->getLocale())</p>
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('explore') }}" class="btn btn-outline-secondary">
                    @autotranslate('Zurück zu den Fragen', app()->getLocale())
                </a>
            </div>
        </div>
    </section>
@endsection

<style scoped>
.explore-results-section { background: #f9fafb; min-height: 100vh; }
.card { border: none; border-radius: 15px; transition: all 0.3s ease; }
.card-img-top { height: 200px; object-fit: cover; border-top-left-radius: 15px; border-top-right-radius: 15px; }
.card-title { color: #2d3748; font-weight: 600; }
.card-text { color: #4a5568; font-size: 0.9rem; }
.hover-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1); }
.btn-primary { background: #3182ce; border: none; }
.btn-outline-secondary { border-color: #3182ce; color: #3182ce; }
.btn-outline-secondary:hover { background: #3182ce; color: white; }
</style>
