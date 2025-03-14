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
        $sessionId = Session::getId(); // Laravel Session-ID
        $ipAddress = $request->ip();
        $pageUrl = $request->fullUrl();

        $session = ModVisitorSession::where('session_id', $sessionId)->first();

        if (!$session) {
            // Neue Sitzung starten
            ModVisitorSession::create([
                'session_id' => $sessionId,
                'ip_address' => $ipAddress,
                'page_url' => $pageUrl,
                'started_at' => now(),
                'last_activity_at' => now(),
            ]);
        } else {
            // Bestehende Sitzung aktualisieren
            $session->update([
                'page_url' => $pageUrl,
                'last_activity_at' => now(),
            ]);
        }

        return $next($request);
    }
}
