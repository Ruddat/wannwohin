<div>
    {{-- Do your work, then step back. --}}

<!-- resources/views/livewire/backend/seo-meta-component/visitor-stats.blade.php -->
<div class="container-xl">
    <!-- Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label">Zeitraum</label>
                    <select wire:model.live="timeRange" class="form-select">
                        <option value="24h">Letzte 24 Stunden</option>
                        <option value="7d">Letzte 7 Tage</option>
                        <option value="30d">Letzte 30 Tage</option>
                        <option value="all">Gesamter Zeitraum</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Quelle</label>
                    <select wire:model.live="sourceFilter" class="form-select">
                        <option value="">Alle Quellen</option>
                        @foreach ($sources as $source)
                            <option value="{{ $source }}">{{ $source }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Übersichtskarten -->
    <div class="row g-4">
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
                    <h5 class="mb-0">
                        Besuche {{ $timeRange === '30d' || $timeRange === 'all' ? 'pro Monat' : 'pro Tag' }}
                        ({{ $timeRange === '24h' ? 'letzte 24h' : ($timeRange === '7d' ? 'letzte 7 Tage' : ($timeRange === '30d' ? 'letzte 30 Tage' : 'gesamt')) }})
                    </h5>
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
                                    <span class="badge bg-primary me-2">{{ $referral->source }}</span>
                                    @if($referral->keyword)
                                        <small class="text-muted">({{ $referral->keyword }})</small>
                                    @endif
                                </span>
                                <span class="badge bg-light text-dark">{{ $referral->count }}</span>
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
                                <a href="{{ $page->landing_page }}" target="_blank" class="text-truncate" style="max-width: 300px;">
                                    {{ $page->landing_page }}
                                </a>
                                <span class="badge bg-light text-dark">{{ $page->count }}</span>
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
        let chart = null;

        // Initialisiere den Chart
        document.addEventListener('DOMContentLoaded', () => {
            const initialOptions = {
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: { show: true },
                },
                series: [{
                    name: 'Besuche',
                    data: @json($chartData),
                }],
                xaxis: {
                    categories: @json($chartLabels),
                    title: { text: '{{ $timeRange === "30d" || $timeRange === "all" ? "Monat" : "Tag" }}' },
                },
                yaxis: {
                    title: { text: 'Anzahl Besuche' },
                },
                colors: ['#0d6efd'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                    },
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                tooltip: {
                    x: { format: '{{ $timeRange === "30d" || $timeRange === "all" ? "MMM yyyy" : "dd.MM" }}' },
                },
            };
            chart = new ApexCharts(document.querySelector('#visits-chart'), initialOptions);
            chart.render();
        });

        // Warte auf Livewire und aktualisiere den Chart
        function waitForLivewire(callback) {
            if (typeof Livewire !== 'undefined') {
                callback();
            } else {
                setTimeout(() => waitForLivewire(callback), 100);
            }
        }

        waitForLivewire(() => {
            Livewire.on('update-chart', ({ labels, data, timeRange }) => {
                if (chart) {
                    chart.updateOptions({
                        xaxis: {
                            categories: labels,
                            title: { text: timeRange === '30d' || timeRange === 'all' ? 'Monat' : 'Tag' },
                        },
                        tooltip: {
                            x: { format: timeRange === '30d' || timeRange === 'all' ? 'MMM yyyy' : 'dd.MM' },
                        },
                    });
                    chart.updateSeries([{ data: data }]);
                }
            });
        });
    </script>
@endpush

</div>
