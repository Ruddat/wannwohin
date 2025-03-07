<?php

namespace App\Http\Controllers\Tools;

use Knp\Snappy\Image;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;

class ScreenshotController extends Controller
{
    public function generate()
    {
        $url = url('/'); // https://wannwohin.test
        $path = public_path('screenshots/startseite.png');

        // Pfad zu wkhtmltoimage.exe anpassen
        $wkhtmltoimagePath = 'C:\wkhtmltoimage\bin\wkhtmltoimage.exe';

        if (!file_exists($wkhtmltoimagePath)) {
            return response()->json(['error' => 'wkhtmltoimage.exe nicht gefunden unter: ' . $wkhtmltoimagePath]);
        }

        $snappy = new Image($wkhtmltoimagePath);
        $snappy->generate($url, $path, [
            'width' => 1280,
            'height' => 800, // Optional, wird ignoriert bei voller Seite
            'crop-h' => null, // Ganzen Seite erfassen
            'format' => 'png', // Sicherstellen, dass PNG ausgegeben wird
        ]);

        return response()->json(['message' => 'Screenshot erstellt', 'path' => $path]);
    }
}
