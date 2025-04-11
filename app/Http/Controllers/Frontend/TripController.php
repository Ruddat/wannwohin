<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Helpers\HeaderHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class TripController extends Controller
{
    public function index()
    {


        $headerData = HeaderHelper::getHeaderContent('explore-trips');
        Session::put('headerData', $headerData);


        return view('frondend.trips-liste.trips-index', [
            'panorama_location_picture' => $headerData['bgImgPath'] ?? asset('img/headers/default-header.jpg'),
            'panorama_location_text' => $headerData['title_text'] ?? 'Finde dein Abenteuer',
        ]);
    }

    public function show($id)
    {
        // Später: Einzeltrip anzeigen
    }
}
