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
                'dwell_time' => max($session->dwell_time, $dwellTime),
                'last_activity_at' => now(),
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
