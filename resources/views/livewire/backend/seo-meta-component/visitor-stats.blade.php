<div>
    {{-- Do your work, then step back. --}}

<!-- resources/views/livewire/backend/seo-meta-component/visitor-stats.blade.php -->
<div class="container-xl">
    <div class="row g-4">
        <!-- Übersichtskarten -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted">Gesamtbesuche</h6>
                    <h3 class="mb-0">{{ number_format($totalVisits) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted">Aktive Sitzungen</h6>
                    <h3 class="mb-0">{{ $totalSessions }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted">Online-Besucher</h6>
                    <h3 class="mb-0">{{ $onlineUsers }}</h3>
                    <small class="text-muted">(letzte 5 Min)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted">Verweildauer</h6>
                    <h3 class="mb-0">{{ number_format($averageDwellTime / 60, 2) }}</h3>
                    <small class="text-muted">Minuten</small>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Besuche pro Stunde (letzte 24h)</h5>
                </div>
                <div class="card-body">
                    <div id="visits-chart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>

        <!-- Top Zuflüsse und Landing Pages -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Top 5 Zuflüsse</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse ($topReferrals as $referral)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <span class="badge me-2">{{ $referral->source }}</span>
                                    @if($referral->keyword)
                                        <small>({{ $referral->keyword }})</small>
                                    @endif
                                </span>
                                <span class="badge">{{ $referral->count }}</span>
                            </li>
                        @empty
                            <li class="list-group-item">Keine Daten verfügbar</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Top 5 Landing Pages</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse ($topLandingPages as $page)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ Str::limit($page->landing_page, 50) }}</span>
                                <span class="badge">{{ $page->count }}</span>
                            </li>
                        @empty
                            <li class="list-group-item">Keine Daten verfügbar</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            var options = {
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: { show: false },
                },
                series: [{
                    name: 'Besuche',
                    data: @json($chartData)
                }],
                xaxis: {
                    categories: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'],
                    title: { text: 'Stunde' }
                },
                yaxis: {
                    title: { text: 'Anzahl Besuche' }
                },
                colors: ['#0d6efd'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                    }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
            };

            var chart = new ApexCharts(document.querySelector("#visits-chart"), options);
            chart.render();
        });
    </script>
@endpush

</div>
