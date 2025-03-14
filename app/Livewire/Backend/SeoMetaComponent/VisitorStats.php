<?php

namespace App\Livewire\Backend\SeoMetaComponent;

use DB;
use Livewire\Component;
use App\Models\ModReferralLog;
use App\Models\ModVisitorSession;

class VisitorStats extends Component
{
    public function render()
    {
        // Referral-Statistiken
        $topReferrals = ModReferralLog::select('source', 'keyword', DB::raw('SUM(visit_count) as count'))
            ->groupBy('source', 'keyword')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
        $totalVisits = ModReferralLog::sum('visit_count');

        // Top Landing Pages
        $topLandingPages = ModReferralLog::select('landing_page', DB::raw('SUM(visit_count) as count'))
            ->groupBy('landing_page')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Visitor-Statistiken
        $onlineUsers = ModVisitorSession::where('last_activity_at', '>=', now()->subMinutes(5))->count();
        $averageDwellTime = ModVisitorSession::avg('dwell_time');
        $totalSessions = ModVisitorSession::count();

        // Besuche pro Stunde (letzte 24 Stunden)
        $visitsPerHour = ModReferralLog::select(
            DB::raw('HOUR(visited_at) as hour'),
            DB::raw('SUM(visit_count) as visits')
        )
            ->where('visited_at', '>=', now()->subHours(24))
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('visits', 'hour')
            ->all();

        // Daten für das Chart vorbereiten
        $hours = range(0, 23);
        $chartData = array_map(function ($hour) use ($visitsPerHour) {
            return $visitsPerHour[$hour] ?? 0;
        }, $hours);

        return view('livewire.backend.seo-meta-component.visitor-stats', [
            'topReferrals' => $topReferrals,
            'totalVisits' => $totalVisits,
            'topLandingPages' => $topLandingPages,
            'onlineUsers' => $onlineUsers,
            'averageDwellTime' => $averageDwellTime,
            'totalSessions' => $totalSessions,
            'chartData' => $chartData,
        ])->layout('backend.layouts.livewiere-main'); // Korrigierter Layout-Name
    }
}
