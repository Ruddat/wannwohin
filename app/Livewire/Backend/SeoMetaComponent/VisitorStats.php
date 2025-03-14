<?php

namespace App\Livewire\Backend\SeoMetaComponent;

use Livewire\Component;
use App\Models\ModReferralLog;
use App\Models\ModVisitorSession;
use Illuminate\Support\Facades\DB;

class VisitorStats extends Component
{
    public $timeRange = '24h';
    public $sourceFilter = '';

    protected $queryString = [
        'timeRange' => ['except' => '24h'],
        'sourceFilter' => ['except' => ''],
    ];

    public function render()
    {
        $startDate = match ($this->timeRange) {
            '24h' => now()->subHours(24),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            'all' => now()->subYears(10),
            default => now()->subHours(24),
        };

        $referralQuery = ModReferralLog::where('visited_at', '>=', $startDate);
        if ($this->sourceFilter) {
            $referralQuery->where('source', $this->sourceFilter);
        }

        $topReferrals = $referralQuery->select('source', 'keyword', DB::raw('SUM(visit_count) as count'))
            ->groupBy('source', 'keyword')
            ->orderByRaw('SUM(visit_count) DESC')
            ->limit(5)
            ->get();
        $totalVisits = $referralQuery->sum('visit_count');

        $topLandingPages = $referralQuery->select('landing_page', DB::raw('SUM(visit_count) as count'))
            ->groupBy('landing_page')
            ->orderByRaw('SUM(visit_count) DESC')
            ->limit(5)
            ->get();

        $visitorQuery = ModVisitorSession::where('last_activity_at', '>=', $startDate);
        $onlineUsers = $visitorQuery->where('last_activity_at', '>=', now()->subMinutes(5))->count();
        $averageDwellTime = $visitorQuery->avg('dwell_time');
        $totalSessions = $visitorQuery->count();

        if ($this->timeRange === '30d' || $this->timeRange === 'all') {
            $visitsData = $referralQuery->select(
                DB::raw('DATE_FORMAT(visited_at, "%Y-%m") as period'),
                DB::raw('SUM(visit_count) as visits')
            )
                ->groupBy('period')
                ->orderBy('period')
                ->pluck('visits', 'period')
                ->all();
            $chartLabels = array_keys($visitsData);
            $chartData = array_values($visitsData);
        } else {
            $visitsData = $referralQuery->select(
                DB::raw('DATE(visited_at) as date'),
                DB::raw('SUM(visit_count) as visits')
            )
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('visits', 'date')
                ->all();
            $chartLabels = array_map(fn($date) => date('d.m.', strtotime($date)), array_keys($visitsData));
            $chartData = array_values($visitsData);
        }

        $sources = ModReferralLog::select('source')->distinct()->pluck('source')->all();

        // Event auslösen, um das Chart zu aktualisieren
        $this->dispatch('update-chart', [
            'labels' => $chartLabels,
            'data' => $chartData,
            'timeRange' => $this->timeRange,
        ]);

        return view('livewire.backend.seo-meta-component.visitor-stats', [
            'topReferrals' => $topReferrals,
            'totalVisits' => $totalVisits,
            'topLandingPages' => $topLandingPages,
            'onlineUsers' => $onlineUsers,
            'averageDwellTime' => $averageDwellTime,
            'totalSessions' => $totalSessions,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'sources' => $sources,
        ])->layout('backend.layouts.livewiere-main');
    }
}
