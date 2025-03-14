<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ModVisitorSession;

class VisitorController extends Controller
{
    public function trackDwellTime(Request $request)
    {
        $sessionId = $request->input('session_id');
        $dwellTime = $request->input('dwell_time');
        $pageUrl = $request->input('page_url');

        $session = ModVisitorSession::where('session_id', $sessionId)->first();
        if ($session) {
            $session->update([
                'dwell_time' => $session->dwell_time + $dwellTime,
                'page_url' => $pageUrl,
                'last_activity_at' => now(),
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
