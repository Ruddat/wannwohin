<!-- resources/views/backend/admin/seo-meta/visitor-stats.blade.php -->
@extends('raadmin.layout.master')

@section('main-content')
    <div class="container-xl">
        <!-- Filter -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <label class="form-label">Zeitraum</label>
                        <select id="timeRange" class="form-select">
                            <option value="24h" {{ $timeRange === '24h' ? 'selected' : '' }}>Letzte 24 Stunden</option>
                            <option value="7d" {{ $timeRange === '7d' ? 'selected' : '' }}>Letzte 7 Tage</option>
                            <option value="30d" {{ $timeRange === '30d' ? 'selected' : '' }}>Letzte 30 Tage</option>
                            <option value="year" {{ $timeRange === 'year' ? 'selected' : '' }}>Aktuelles Jahr</option>
                            <option value="all" {{ $timeRange === 'all' ? 'selected' : '' }}>Gesamter Zeitraum</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jahr</label>
                        <select id="yearFilter" class="form-select">
                            <option value="">Alle Jahre</option>
                            @foreach ($years as $year)
                                <option value="{{ $year }}" {{ $yearFilter == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Monat</label>
                        <select id="monthFilter" class="form-select">
                            <option value="">Alle Monate</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $monthFilter == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quelle</label>
                        <select id="sourceFilter" class="form-select">
                            <option value="">Alle Quellen</option>
                            @foreach ($sources as $source)
                                <option value="{{ $source }}" {{ $sourceFilter === $source ? 'selected' : '' }}>{{ $source }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Landing Page</label>
                        <select id="landingPageFilter" class="form-select">
                            <option value="">Alle Seiten</option>
                            @foreach ($landingPages as $page)
                                <option value="{{ $page }}" {{ $landingPageFilter === $page ? 'selected' : '' }}>{{ $page }}</option>
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
                        <h3 class="mb-0" id="totalVisits">{{ number_format($totalVisits) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="card-title text-muted">Aktive Sitzungen</h6>
                        <h3 class="mb-0" id="totalSessions">{{ $totalSessions }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="card-title text-muted">Online-Besucher</h6>
                        <h3 class="mb-0" id="onlineUsers">{{ $onlineUsers }}</h3>
                        <small class="text-muted">(letzte 5 Min)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="card-title text-muted">Verweildauer</h6>
                        <h3 class="mb-0" id="averageDwellTime">{{ number_format($averageDwellTime / 60, 2) }}</h3>
                        <small class="text-muted">Minuten</small>
                    </div>
                </div>
            </div>

            <!-- Chart -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0" id="chartTitle">
                            Besuche {{ ($yearFilter && $monthFilter) ? 'pro Tag' : ($timeRange === 'year' || $timeRange === 'all' ? 'pro Monat' : 'pro Tag') }}
                            ({{ $yearFilter && $monthFilter ? date('F Y', mktime(0, 0, 0, $monthFilter, 1, $yearFilter)) : ($timeRange === '24h' ? 'letzte 24h' : ($timeRange === '7d' ? 'letzte 7 Tage' : ($timeRange === '30d' ? 'letzte 30 Tage' : ($timeRange === 'year' ? 'dieses Jahr' : 'gesamt')))) }})
                            {{ $landingPageFilter ? " - $landingPageFilter" : '' }}
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
                        <ul class="list-group list-group-flush" id="topReferrals">
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
                        <ul class="list-group list-group-flush" id="topLandingPages">
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
                    title: { text: '{{ ($yearFilter && $monthFilter) ? "Tag" : ($timeRange === "year" || $timeRange === "all" ? "Monat" : "Tag") }}' },
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
                    x: { format: '{{ ($yearFilter && $monthFilter) ? "dd.MM" : ($timeRange === "year" || $timeRange === "all" ? "MMM yyyy" : "dd.MM") }}' },
                },
            };
            chart = new ApexCharts(document.querySelector('#visits-chart'), initialOptions);
            chart.render();

            // Filter-Event-Listener
            document.getElementById('timeRange').addEventListener('change', updateStats);
            document.getElementById('sourceFilter').addEventListener('change', updateStats);
            document.getElementById('yearFilter').addEventListener('change', updateStats);
            document.getElementById('monthFilter').addEventListener('change', updateStats);
            document.getElementById('landingPageFilter').addEventListener('change', updateStats);
        });

        // AJAX-Aufruf für Updates
        function updateStats() {
            const timeRange = document.getElementById('timeRange').value;
            const sourceFilter = document.getElementById('sourceFilter').value;
            const yearFilter = document.getElementById('yearFilter').value;
            const monthFilter = document.getElementById('monthFilter').value;
            const landingPageFilter = document.getElementById('landingPageFilter').value;
            const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenElement ? csrfTokenElement.content : null;

            if (!csrfToken) {
                console.error('CSRF-Token nicht gefunden!');
                return;
            }

            fetch(`/verwaltung/seo-table-manager/visitor-stats?timeRange=${timeRange}&sourceFilter=${sourceFilter}&year=${yearFilter}&month=${monthFilter}&landingPageFilter=${landingPageFilter}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Netzwerkantwort war nicht ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                // Aktualisiere Statistiken
                document.getElementById('totalVisits').textContent = new Intl.NumberFormat().format(data.totalVisits);
                document.getElementById('totalSessions').textContent = data.totalSessions;
                document.getElementById('onlineUsers').textContent = data.onlineUsers;
                document.getElementById('averageDwellTime').textContent = (data.averageDwellTime / 60).toFixed(2);

                // Aktualisiere Top-Listen
                updateList('topReferrals', data.topReferrals, referral => `
                    <span class="badge bg-primary me-2">${referral.source}</span>
                    ${referral.keyword ? `<small class="text-muted">(${referral.keyword})</small>` : ''}
                    <span class="badge bg-light text-dark">${referral.count}</span>
                `);
                updateList('topLandingPages', data.topLandingPages, page => `
                    <a href="${page.landing_page}" target="_blank" class="text-truncate" style="max-width: 300px;">${page.landing_page}</a>
                    <span class="badge bg-light text-dark">${page.count}</span>
                `);

                // Aktualisiere Chart-Titel
                const periodText = (data.year && data.month) ? 'pro Tag' : (data.timeRange === 'year' || data.timeRange === 'all' ? 'pro Monat' : 'pro Tag');
                const rangeText = data.year && data.month ? `${new Date(data.year, data.month - 1).toLocaleString('default', { month: 'long' })} ${data.year}` :
                                  data.timeRange === '24h' ? 'letzte 24h' :
                                  data.timeRange === '7d' ? 'letzte 7 Tage' :
                                  data.timeRange === '30d' ? 'letzte 30 Tage' :
                                  data.timeRange === 'year' ? 'dieses Jahr' : 'gesamt';
                document.getElementById('chartTitle').textContent = `Besuche ${periodText} (${rangeText})${data.landingPageFilter ? ' - ' + data.landingPageFilter : ''}`;

                // Aktualisiere Chart
                chart.updateOptions({
                    xaxis: {
                        categories: data.chartLabels,
                        title: { text: (data.year && data.month) ? 'Tag' : (data.timeRange === 'year' || data.timeRange === 'all' ? 'Monat' : 'Tag') },
                    },
                    tooltip: {
                        x: { format: (data.year && data.month) ? 'dd.MM' : (data.timeRange === 'year' || data.timeRange === 'all' ? 'MMM yyyy' : 'dd.MM') },
                    },
                });
                chart.updateSeries([{ data: data.chartData }]);
            })
            .catch(error => console.error('Fehler beim Laden der Statistiken:', error));
        }

        // Hilfsfunktion zum Aktualisieren von Listen
        function updateList(elementId, items, templateFn) {
            const list = document.getElementById(elementId);
            list.innerHTML = items.length ? items.map(item => `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${templateFn(item)}
                </li>
            `).join('') : '<li class="list-group-item">Keine Daten verfügbar</li>';
        }
    </script>
@endsection
