@extends('backend.layouts.main')

@section('main-content')
<div class="container-xl">
    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="page-title text-center">Dashboard Overview</h1>
        </div>
    </div>

    <!-- Statistiken Cards -->
    <div class="row row-cards mb-3">
        <!-- Total Locations -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Locations</div>
                        <div class="ms-auto lh-1 text-muted">
                            <i class="icon icon-map-pin text-primary"></i>
                        </div>
                    </div>
                    <div class="h1 mb-3">{{ $totalLocations }}</div>
                    <div class="d-flex">
                        <div>Locations in the system</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Countries -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Countries</div>
                        <div class="ms-auto lh-1 text-muted">
                            <i class="icon icon-globe text-success"></i>
                        </div>
                    </div>
                    <div class="h1 mb-3">{{ $totalCountries }}</div>
                    <div class="d-flex">
                        <div>Countries covered</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Parks -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Parks</div>
                        <div class="ms-auto lh-1 text-muted">
                            <i class="icon icon-tree text-info"></i>
                        </div>
                    </div>
                    <div class="h1 mb-3">{{ $totalParks }}</div>
                    <div class="d-flex">
                        <div>Parks in the database</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Images -->
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Total Images</div>
                        <div class="ms-auto lh-1 text-muted">
                            <i class="icon icon-photo text-warning"></i>
                        </div>
                    </div>
                    <div class="h1 mb-3">{{ $totalImages }}</div>
                    <div class="d-flex">
                        <div>Images uploaded</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weitere Inhalte (Traffic Summary, Top 10 Locations, Map) -->
    <div class="row">
        <!-- Traffic Summary -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header">
                    <h3 class="card-title text-primary">Traffic Summary</h3>
                </div>
                <div class="card-body">
                    <div id="trafficSummaryChart"></div>
                </div>
            </div>
        </div>

        <!-- Top 10 Locations -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top 10 Locations</h3>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th>Search Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topLocations as $location)
                                <tr>
                                    <td>{{ $location->title }}</td>
                                    <td>{{ $location->search_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Locations Map -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Locations Map</h3>
                </div>
                <div class="card-body">
                    <div id="map-world" class="ratio ratio-16x9"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daten für JavaScript -->
<script>
    window.trafficSummaryData = {!! json_encode($trafficSummary->pluck('total_searches')->toArray()) !!};
    window.trafficSummaryMonths = {!! json_encode($trafficSummary->pluck('month')->toArray()) !!};
    window.topLocations = {!! json_encode($topLocations) !!};
</script>
@endsection

@push('scripts')
@vite(['resources/backend/js/app.js'])
@endpush

@push('styles')
<style>
    #map-world {
        height: 100%;
        width: 100%;
        border-radius: 8px;
    }
    .card {
        border-radius: 8px;
    }
    .row-deck {
        gap: 1rem;
    }
    #trafficSummaryChart {
        width: 100%;
        max-width: 100%;
        height: 100%;
    }
</style>
@endpush
