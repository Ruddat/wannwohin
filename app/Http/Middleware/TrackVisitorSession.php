<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ModVisitorSession;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitorSession
{
    public function handle(Request $request, Closure $next)
    {
        if (
            !$request->isMethod('GET') ||
            $request->routeIs('track.dwell.time') ||
            $request->is('track-dwell-time') ||
            auth('admin')->check() ||
            $request->is('admin/*') ||
            $request->is('livewire/*')
        ) {
            return $next($request);
        }

        $sessionId = Session::getId();
        $ipAddress = $request->ip();
        $pageUrl = $request->fullUrl();

        $session = ModVisitorSession::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'ip_address' => $ipAddress,
                'page_url' => $pageUrl,
                'started_at' => now(),
                'last_activity_at' => now(),
            ]
        );

        $session->update([
            'page_url' => $pageUrl,
            'last_activity_at' => now(),
        ]);

        return $next($request);
    }
}
