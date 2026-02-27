<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AmusementParks;
use Illuminate\Http\Request;

class ParkController extends Controller
{
    public function show(string $slug)
    {
        $park = AmusementParks::where('slug', $slug)
            ->orWhere('external_id', $slug)
            ->firstOrFail();

        return view('frontend.parks.show', compact('park'));
    }
}
