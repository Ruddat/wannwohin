<?php

namespace App\Http\Controllers\Backend\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocationImageController extends Controller
{
    public function index()
    {
        $locations = WwdeLocation::with('primaryImage')->get();
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:50',
            'continent_id' => 'nullable|exists:wwde_continents,id',
            'country_id' => 'nullable|exists:wwde_countries,id',
            'primary_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:20480', // 20 MB
        ]);

        // Location speichern
        $location = WwdeLocation::create($request->only([
            'title', 'continent_id', 'country_id'
        ]));

        // Primärbild speichern
        if ($request->hasFile('primary_image')) {
            $imagePath = $this->storeImage($request->file('primary_image'), 'primary');
            WwdeLocationImages::create([
                'location_id' => $location->id,
                'image_path' => $imagePath,
                'is_primary' => true,
            ]);
        }

        return redirect()->route('locations.index')->with('success', 'Location created successfully!');
    }

    private function storeImage($image, $type)
    {
        $filename = $type . '_' . time() . '.' . $image->getClientOriginalExtension();
        $path = "locations/$filename";

        // Bildgröße anpassen und speichern
        $img = Image::make($image->getPathname());
        $img->resize(1920, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        Storage::disk('public')->put($path, (string) $img->encode());

        return $path;
    }
}
