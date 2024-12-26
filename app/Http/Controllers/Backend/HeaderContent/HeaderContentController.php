<?php

namespace App\Http\Controllers\Backend\HeaderContent;

use Illuminate\Http\Request;
use App\Models\HeaderContent;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class HeaderContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $headerContents = HeaderContent::all();
        return view('backend.admin.header.index', compact('headerContents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admin.header.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bg_img' => 'required|image|mimes:jpeg,png,jpg,webp|max:40960', // Max. 40 MB, inkl. WebP
            'main_img' => 'required|image|mimes:jpeg,png,jpg,webp|max:40960', // Max. 40 MB, inkl. WebP
            'main_text' => 'required|string',
            'title' => 'nullable|string',
        ]);

        // Hintergrund- und Hauptbild verarbeiten
        $bgImgPath = $this->processImage($request->file('bg_img'), 'bg', 1970, 550);
        $mainImgPath = $this->processImage($request->file('main_img'), 'main', 718, 982);

        // Editor-Inhalt bereinigen
        $cleanedText = Purifier::clean($request->main_text);

        // Daten speichern
        HeaderContent::create([
            'bg_img' => $bgImgPath,
            'main_img' => $mainImgPath,
            'main_text' => $cleanedText,
            'title' => $request->title,
        ]);

        // Flash-Nachricht
        session()->flash('success', 'Header content created successfully!');

        return redirect()->route('header-manager.header_contents.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HeaderContent $headerContent)
    {
        return view('backend.admin.header.edit', compact('headerContent'));
    }

    /**
     * Update the specified resource in storage.
     */

     public function update(Request $request, HeaderContent $headerContent)
     {
         $request->validate([
             'bg_img' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:40960', // Max. 40 MB, inkl. WebP
             'main_img' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:40960', // Max. 40 MB, inkl. WebP
             'main_text' => 'required|string',
             'title' => 'nullable|string',
         ]);

         // Hintergrundbild aktualisieren
         if ($request->hasFile('bg_img')) {
             Storage::disk('public')->delete($headerContent->bg_img);
             $headerContent->bg_img = $this->processImage($request->file('bg_img'), 'bg', 1970, 550);
         }

         // Hauptbild aktualisieren
         if ($request->hasFile('main_img')) {
             Storage::disk('public')->delete($headerContent->main_img);
             $headerContent->main_img = $this->processImage($request->file('main_img'), 'main', 718, 982);
         }

         // Editor-Inhalt bereinigen
         $cleanedText = Purifier::clean($request->main_text);

         // Daten aktualisieren
         $headerContent->update([
             'main_text' => $cleanedText,
             'title' => $request->title,
         ]);

         // Flash-Nachricht
         session()->flash('success', 'Header content updated successfully!');

         return redirect()->route('header-manager.header_contents.index');
     }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HeaderContent $headerContent)
    {
        if ($headerContent->bg_img) {
            Storage::disk('public')->delete($headerContent->bg_img);
        }
        if ($headerContent->main_img) {
            Storage::disk('public')->delete($headerContent->main_img);
        }

        $headerContent->delete();

        // Flash-Nachricht
        session()->flash('success', 'Header content deleted successfully!');

        return redirect()->route('header-manager.header_contents.index');
    }

    /**
     * Process and save an image with Intervention Image.
     */
    private function processImage($image, $type, $width, $height)
    {
        $filename = $type . '_' . time() . '.' . $image->getClientOriginalExtension();
        $relativePath = "uploads/images/startpage/$filename";

        // Zielordner erstellen, falls er nicht existiert
        $directory = storage_path('app/public/uploads/images/startpage');
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true); // Erstelle Ordner mit Unterordnern
        }

        // Bildgröße anpassen (mit Seitenverhältnis)
        $img = Image::read($image->getPathname());
        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize(); // Verhindert Vergrößerung kleinerer Bilder
        });

        // Bild speichern
        $img->save("$directory/$filename");

        return $relativePath;
    }

}
