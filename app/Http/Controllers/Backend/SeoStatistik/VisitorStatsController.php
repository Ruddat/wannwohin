<?php

namespace App\Http\Controllers\Backend\SeoStatistik;

use Illuminate\Http\Request;
use App\Models\ModReferralLog;
use App\Models\ModVisitorSession;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class VisitorStatsController extends Controller
{
    public function index(Request $request)
    {
        $timeRange = $request->query('timeRange', '24h');
        $sourceFilter = $request->query('sourceFilter', '');
        $yearFilter = $request->query('year', ''); // Neuer Filter für Jahr
        $monthFilter = $request->query('month', ''); // Neuer Filter für Monat
        $landingPageFilter = $request->query('landingPageFilter', ''); // Neuer Filter für Landing Page

        // Startdatum basierend auf timeRange oder Jahr/Monat
        if ($yearFilter && $monthFilter) {
            $startDate = Carbon::create($yearFilter, $monthFilter, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
        } else {
            $startDate = match ($timeRange) {
                '24h' => now()->subHours(24),
                '7d' => now()->subDays(7),
                '30d' => now()->subDays(30),
                'year' => now()->startOfYear(), // Neuer Zeitbereich für aktuelles Jahr
                'all' => now()->subYears(10),
                default => now()->subHours(24),
            };
            $endDate = now();
        }

        $referralQuery = ModReferralLog::whereBetween('visited_at', [$startDate, $endDate]);
        if ($sourceFilter) {
            $referralQuery->where('source', $sourceFilter);
        }
        if ($landingPageFilter) {
            $referralQuery->where('landing_page', $landingPageFilter);
        }

        // Referral-Statistiken
        $topReferrals = $referralQuery->select('source', 'keyword', DB::raw('SUM(visit_count) as count'))
            ->groupBy('source', 'keyword')
            ->orderByRaw('SUM(visit_count) DESC')
            ->limit(5)
            ->get();
        $totalVisits = $referralQuery->sum('visit_count');

        // Top Landing Pages
        $topLandingPages = $referralQuery->select('landing_page', DB::raw('SUM(visit_count) as count'))
            ->groupBy('landing_page')
            ->orderByRaw('SUM(visit_count) DESC')
            ->limit(5)
            ->get();

        // Visitor-Statistiken
        $visitorQuery = ModVisitorSession::whereBetween('last_activity_at', [$startDate, $endDate]);
        $onlineUsers = $visitorQuery->where('last_activity_at', '>=', now()->subMinutes(5))->count();
        $averageDwellTime = $visitorQuery->avg('dwell_time');
        $totalSessions = $visitorQuery->count();

        // Chart-Daten
        if ($yearFilter && $monthFilter) {
            // Tagesdaten für den ausgewählten Monat
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
        } elseif ($timeRange === 'year' || $timeRange === 'all') {
            // Monatsdaten für das Jahr oder alle Jahre
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
            // Tagesdaten für 24h, 7d, 30d
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

        // Zusätzliche Daten für Filter
        $sources = ModReferralLog::select('source')->distinct()->pluck('source')->all();
        $landingPages = ModReferralLog::select('landing_page')->distinct()->pluck('landing_page')->all();
        $years = ModReferralLog::selectRaw('YEAR(visited_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->all();

        if ($request->ajax()) {
            return response()->json([
                'topReferrals' => $topReferrals,
                'totalVisits' => $totalVisits,
                'topLandingPages' => $topLandingPages,
                'onlineUsers' => $onlineUsers,
                'averageDwellTime' => $averageDwellTime,
                'totalSessions' => $totalSessions,
                'chartLabels' => $chartLabels,
                'chartData' => $chartData,
                'sources' => $sources,
                'landingPages' => $landingPages,
                'years' => $years,
                'timeRange' => $timeRange,
                'year' => $yearFilter,
                'month' => $monthFilter,
            ]);
        }

        return view('backend.admin.seo-meta.visitor-stats', compact(
            'topReferrals',
            'totalVisits',
            'topLandingPages',
            'onlineUsers',
            'averageDwellTime',
            'totalSessions',
            'chartLabels',
            'chartData',
            'sources',
            'landingPages',
            'years',
            'timeRange',
            'sourceFilter',
            'yearFilter',
            'monthFilter',
            'landingPageFilter'
        ));
    }
}
