@extends('raadmin.layout.master')

@section('main-content')
<div class="page-body">
    <div class="container-xl">
        <!-- Dashboard Header -->
        <div class="page-header d-print-none mb-4">
            <div class="row align-items-center">
                <div class="col text-center">
                    <h2 class="page-title fw-bold animate__animated animate__fadeIn">Dashboard Overview</h2>
                    <div class="text-muted mt-1 animate__animated animate__fadeIn animate__delay-1s">Your control center at a glance</div>
                </div>
            </div>
        </div>

        <!-- Statistiken Cards -->
        <div class="row g-4 mb-4 animate__animated animate__fadeInUp">
            <!-- Total Locations -->
            <div class="col-sm-6 col-lg-3">
                <div class="card bg-primary-lt h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="avatar avatar-sm rounded-circle bg-primary">
                                    <svg class="icon" width="24" height="24"><use xlink:href="/tabler-icons.svg#map-pin" /></svg>
                                </span>
                            </div>
                            <div>
                                <div class="text-muted small mb-1">Total Locations</div>
                                <div class="h1 mb-0">{{ $totalLocations }}</div>
                                <div class="text-muted small">Locations in the system</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Countries -->
            <div class="col-sm-6 col-lg-3">
                <div class="card bg-success-lt h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="avatar avatar-sm rounded-circle bg-success">
                                    <svg class="icon" width="24" height="24"><use xlink:href="/tabler-icons.svg#globe" /></svg>
                                </span>
                            </div>
                            <div>
                                <div class="text-muted small mb-1">Total Countries</div>
                                <div class="h1 mb-0">{{ $totalCountries }}</div>
                                <div class="text-muted small">Countries covered</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Parks -->
            <div class="col-sm-6 col-lg-3">
                <div class="card bg-info-lt h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="avatar avatar-sm rounded-circle bg-info">
                                    <svg class="icon" width="24" height="24"><use xlink:href="/tabler-icons.svg#tree" /></svg>
                                </span>
                            </div>
                            <div>
                                <div class="text-muted small mb-1">Total Parks</div>
                                <div class="h1 mb-0">{{ $totalParks }}</div>
                                <div class="text-muted small">Parks in the database</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Images -->
            <div class="col-sm-6 col-lg-3">
                <div class="card bg-warning-lt h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="avatar avatar-sm rounded-circle bg-warning">
                                    <svg class="icon" width="24" height="24"><use xlink:href="/tabler-icons.svg#photo" /></svg>
                                </span>
                            </div>
                            <div>
                                <div class="text-muted small mb-1">Total Images</div>
                                <div class="h1 mb-0">{{ $totalImages }}</div>
                                <div class="text-muted small">Images uploaded</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weitere Inhalte -->
        <div class="row g-4">
            <!-- Traffic Summary -->
            <div class="col-lg-4">
                <div class="card animate__animated animate__fadeInLeft">
                    <div class="card-header border-bottom-0 bg-light">
                        <h3 class="card-title fw-bold">Traffic Summary</h3>
                    </div>
                    <div class="card-body">
                        <div id="trafficSummaryChart" class="chart" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>

            <!-- Top 10 Locations -->
            <div class="col-lg-4">
                <div class="card animate__animated animate__fadeInUp">
                    <div class="card-header border-bottom-0 bg-light">
                        <h3 class="card-title fw-bold">Top 10 Locations</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-vcenter table-hover text-nowrap">
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
                                                <span class="badge bg-blue-lt">{{ $location->search_count }}</span>
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
            <div class="col-lg-4">
                <div class="card animate__animated animate__fadeInRight">
                    <div class="card-header border-bottom-0 bg-light">
                        <h3 class="card-title fw-bold">Locations Map</h3>
                    </div>
                    <div class="card-body">
                        <div id="map-world" class="ratio ratio-16x9 rounded"></div>
                    </div>
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
    .card { transition: transform 0.3s ease; }
    .card:hover { transform: translateY(-5px); }
    .chart { min-height: 300px; }
    #map-world { border-radius: 8px; }
</style>
@endpush
