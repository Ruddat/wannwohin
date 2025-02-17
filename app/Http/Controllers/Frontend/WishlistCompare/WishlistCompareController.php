<?php

namespace App\Http\Controllers\Frontend\WishlistCompare;

use App\Models\WwdeLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WishlistCompareController extends Controller
{
    public function index($slugs)
    {
        // Slugs aus der URL extrahieren (Beispiel: 'berlin-hamburg-muenchen' -> ['berlin', 'hamburg', 'muenchen'])
        $slugArray = explode('-', $slugs);

        // Locations anhand der Slugs abrufen
        $locations = WwdeLocation::whereIn('slug', $slugArray)->get();

        return view('frontend.wishlist-compare.index', compact('locations', 'slugs'));
    }
}
