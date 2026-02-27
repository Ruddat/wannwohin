<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AmusementParks;
use Illuminate\Http\Request;

class ParkAffiliateController extends Controller
{
    public function go(string $slug, string $provider)
    {
        $park = AmusementParks::where('external_id', $slug)
            ->firstOrFail();

        // Beispiel: primary Affiliate URL
        $url = $park->affiliate_url_primary ?? $park->website;

        if (!$url) {
            abort(404);
        }

        return redirect()->away($url);
    }
}
