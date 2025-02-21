@extends('layouts.main')

@section('content')
    <div class="container mt-4">
        <!-- 🔍 Vergleichsüberschrift -->
        <div class="d-flex align-items-center mb-4">
            <i class="fas fa-balance-scale text-primary fa-2x me-2"></i>
            <h1 class="fw-bold m-0">Vergleichsliste</h1>
        </div>

        <!-- 📊 Vergleichstabelle in Card -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h4 class="m-0">
                    <i class="fas fa-map-marker-alt me-2"></i> Deine ausgewählten Reiseziele
                </h4>
            </div>
            <div class="card-body">
                <!-- 🚀 Livewire-Komponente mit Slugs -->
                @livewire('frontend.wishlist-select.wishlist-compare-component', ['slugs' => implode('-', $locations->pluck('slug')->toArray())])
            </div>
        </div>
    </div>
</br>
@endsection
<style>
    /* 🏷️ Vergleichstabelle Styling */
.card {
    border-radius: 10px;
    overflow: hidden;
}

.card-header {
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.card-body {
    background: #f8f9fa;
    padding: 20px;
}

/* 📱 Mobile Optimierung */
@media (max-width: 768px) {
    .card-body {
        padding: 15px;
    }

    h1 {
        font-size: 1.5rem;
    }
}
</style>
