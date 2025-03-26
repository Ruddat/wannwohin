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
    public function index()
    {
        $headerContents = HeaderContent::all();
        return view('backend.admin.header.index', compact('headerContents'));
    }

    public function create()
    {
        return view('backend.admin.header.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'bg_img' => 'required|image|mimes:jpeg,png,jpg,webp|max:40960',
            'main_img' => 'required|image|mimes:jpeg,png,jpg,webp|max:40960',
            'main_text' => 'required|string',
            'title' => 'nullable|string',
            'slug' => 'required|string|unique:header_contents,slug|regex:/^[a-z0-9\-]+$/|max:50', // Pflichtfeld
        ]);

        $bgImgPath = $this->processImage($request->file('bg_img'), 'bg', 1970, 550);
        $mainImgPath = $this->processImage($request->file('main_img'), 'main', 718, 982);
        $cleanedText = Purifier::clean($request->main_text);

        HeaderContent::create([
            'bg_img' => $bgImgPath,
            'main_img' => $mainImgPath,
            'main_text' => $cleanedText,
            'title' => $request->title,
            'slug' => $request->slug, // Muss jetzt manuell gesetzt werden
        ]);

        return redirect()->route('verwaltung.site-manager.header_contents.index')
            ->with('toast', ['type' => 'success', 'message' => 'Header content created successfully!']);
    }

    public function edit(HeaderContent $headerContent)
    {
        return view('backend.admin.header.edit', compact('headerContent'));
    }

    public function update(Request $request, HeaderContent $headerContent)
    {
        $request->validate([
            'bg_img' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:40960',
            'main_img' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:40960',
            'main_text' => 'required|string',
            'title' => 'nullable|string',
            'slug' => 'required|string|unique:header_contents,slug,' . $headerContent->id . '|regex:/^[a-z0-9\-]+$/|max:50', // Pflichtfeld
        ]);

        if ($request->hasFile('bg_img')) {
            Storage::disk('public')->delete($headerContent->bg_img);
            $headerContent->bg_img = $this->processImage($request->file('bg_img'), 'bg', 1970, 550);
        }

        if ($request->hasFile('main_img')) {
            Storage::disk('public')->delete($headerContent->main_img);
            $headerContent->main_img = $this->processImage($request->file('main_img'), 'main', 718, 982);
        }

        $cleanedText = Purifier::clean($request->main_text);

        $headerContent->update([
            'main_text' => $cleanedText,
            'title' => $request->title,
            'slug' => $request->slug,
        ]);

        return redirect()->route('verwaltung.site-manager.header_contents.index')
            ->with('toast', ['type' => 'success', 'message' => 'Header content updated successfully!']);
    }

    public function destroy(HeaderContent $headerContent)
    {
        if ($headerContent->bg_img) {
            Storage::disk('public')->delete($headerContent->bg_img);
        }
        if ($headerContent->main_img) {
            Storage::disk('public')->delete($headerContent->main_img);
        }

        $headerContent->delete();

        return redirect()->route('verwaltung.site-manager.header_contents.index')
            ->with('toast', ['type' => 'success', 'message' => 'Header content deleted successfully!']);
    }

    private function processImage($image, $type, $width, $height)
    {
        $filename = $type . '_' . time() . '.' . $image->getClientOriginalExtension();
        $relativePath = "uploads/images/startpage/$filename";

        $directory = storage_path('app/public/uploads/images/startpage');
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $img = Image::read($image->getPathname());
        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $img->save("$directory/$filename");

        return $relativePath;
    }
}
