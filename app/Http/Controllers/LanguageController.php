<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class LanguageController extends Controller
{
    public function switch(Request $request)
    {
        $locale = $request->input('lang');

        if (array_key_exists($locale, Config::get('app.available_locales'))) {
            return redirect()->back()->withCookie(cookie()->forever('locale', $locale));
        }

        return redirect()->back()->with('error', 'Invalid language selected');
    }
}
