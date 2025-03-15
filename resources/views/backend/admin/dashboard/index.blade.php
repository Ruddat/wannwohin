@extends('backend.layouts.main')

@section('main-content')
<div class="container-xl">
    <!-- Dashboard Header -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title text-center animate__animated animate__fadeIn">Dashboard Overview</h2>
                <div class="text-muted text-center mt-1 animate__animated animate__fadeIn animate__delay-1s">Your control center at a glance</div>
            </div>
        </div>
    </div>

    <!-- Statistiken Cards -->
    <div class="row row-cards mb-4 animate__animated animate__fadeInUp">
        <!-- Total Locations -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-stats bg-primary-lt">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar rounded-circle bg-primary">
                                <svg class="icon" width="24" height="24"><use xlink:href="/tabler-icons.svg#map-pin" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="subheader text-muted">Total Locations</div>
                            <div class="h1 mb-0">{{ $totalLocations }}</div>
                            <div class="text-muted small">Locations in the system</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Countries -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-stats bg-success-lt">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar rounded-circle bg-success">
                                <svg class="icon" width="24" height="24"><use xlink:href="/tabler-icons.svg#globe" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="subheader text-muted">Total Countries</div>
                            <div class="h1 mb-0">{{ $totalCountries }}</div>
                            <div class="text-muted small">Countries covered</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Parks -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-stats bg-info-lt">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar rounded-circle bg-info">
                                <svg class="icon" width="24" height="24"><use xlink:href="/tabler-icons.svg#tree" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="subheader text-muted">Total Parks</div>
                            <div class="h1 mb-0">{{ $totalParks }}</div>
                            <div class="text-muted small">Parks in the database</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Images -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-stats bg-warning-lt">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar rounded-circle bg-warning">
                                <svg class="icon" width="24" height="24"><use xlink:href="/tabler-icons.svg#photo" /></svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="subheader text-muted">Total Images</div>
                            <div class="h1 mb-0">{{ $totalImages }}</div>
                            <div class="text-muted small">Images uploaded</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weitere Inhalte -->
    <div class="row row-cards">
        <!-- Traffic Summary -->
        <div class="col-lg-4 mb-4">
            <div class="card animate__animated animate__fadeInLeft">
                <div class="card-header border-0">
                    <h3 class="card-title">Traffic Summary</h3>
                </div>
                <div class="card-body">
                    <div id="trafficSummaryChart" class="chart"></div>
                </div>
            </div>
        </div>

        <!-- Top 10 Locations -->
        <div class="col-lg-4 mb-4">
            <div class="card animate__animated animate__fadeInUp">
                <div class="card-header border-0">
                    <h3 class="card-title">Top 10 Locations</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-vcenter table-hover card-table">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th class="text-end">Search Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topLocations as $location)
                                    <tr>
                                        <td>{{ $location->title }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-blue">{{ $location->search_count }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Locations Map -->
        <div class="col-lg-4 mb-4">
            <div class="card animate__animated animate__fadeInRight">
                <div class="card-header border-0">
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
    .card-stats { transition: transform 0.3s ease; }
    .card-stats:hover { transform: translateY(-5px); }
    .chart { min-height: 300px; }
    #map-world { border-radius: 8px; }
</style>
@endpush
