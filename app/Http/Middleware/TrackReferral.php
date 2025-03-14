<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ModReferralLog;
use Symfony\Component\HttpFoundation\Response;

class TrackReferral
{
    public function handle(Request $request, Closure $next)
    {
        $referer = $request->header('referer');
        $landingPage = $request->fullUrl();
        $ipAddress = $request->ip();
        $userId = auth()->id();

        $source = 'direct';
        $keyword = null;

        if ($referer) {
            $parsedReferer = parse_url($referer);
            $host = $parsedReferer['host'] ?? '';

            if (str_contains($host, 'google')) {
                $source = 'google';
            } elseif (str_contains($host, 'bing')) {
                $source = 'bing';
            } elseif (str_contains($host, 'yahoo')) {
                $source = 'yahoo';
            }

            if (isset($parsedReferer['query'])) {
                parse_str($parsedReferer['query'], $params);
                $keyword = $params['q'] ?? null;
            }
        }

        // Prüfe, ob ein identischer Eintrag existiert
        $existingLog = ModReferralLog::where([
            'user_id' => $userId,
            'referer_url' => $referer,
            'source' => $source,
            'keyword' => $keyword,
            'landing_page' => $landingPage,
            'ip_address' => $ipAddress,
        ])->first();

        if ($existingLog) {
            // Erhöhe den Zähler und aktualisiere visited_at
            $existingLog->increment('visit_count');
            $existingLog->update(['visited_at' => now()]);
        } else {
            // Erstelle einen neuen Eintrag
            ModReferralLog::create([
                'user_id' => $userId,
                'referer_url' => $referer,
                'source' => $source,
                'keyword' => $keyword,
                'landing_page' => $landingPage,
                'ip_address' => $ipAddress,
                'visited_at' => now(),
                'visit_count' => 1,
            ]);
        }

        return $next($request);
    }
}
