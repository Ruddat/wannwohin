<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    public function switch($lang)
    {
        $locale = $lang;
//dd($locale);
        // Prüfen, ob die Sprache in den verfügbaren Sprachen definiert ist
        if (array_key_exists($locale, Config::get('app.available_locales'))) {
            // Setze die Sprache in einem Cookie, das für ein Jahr gültig ist
            return redirect()->back()->withCookie(cookie()->forever('locale', $locale));
        }

        // Wenn die Sprache ungültig ist, Seite einfach neu laden
        return redirect()->back()->with('error', 'Invalid language selected');
    }
}
