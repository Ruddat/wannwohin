<?php

namespace App\Http\Controllers\Backend\Imports;

use App\Models\WwdeLocation;
use Illuminate\Http\Request;
use App\Models\ModLocationFilter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FilterLocationImportController extends Controller
{
    public function index()
    {
        return view('backend.imports.location-text-import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:10240',
        ]);

        $file = $request->file('file');
        $filename = time() . '-' . $file->getClientOriginalName();
        $path = $file->storeAs('public/uploads', $filename);


        if (!$path) {
            return back()->with('error', 'Datei konnte nicht gespeichert werden.');
        }

        $fullPath = storage_path("app/public/{$path}");

        Log::info("Gespeicherte Datei: {$fullPath}"); // Logge den Speicherpfad

        if (!file_exists($fullPath)) {
            return back()->with('error', 'Gespeicherte Datei nicht gefunden: ' . $fullPath);
        }

        try {
            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

//dd($rows);

            if (!empty($rows)) {
                $this->processData($rows);
            }

            return back()->with('message', 'Datei erfolgreich importiert.');
        } catch (\Exception $e) {
            Log::error("Excel-Import Fehler: " . $e->getMessage());
            return back()->with('error', 'Fehler beim Verarbeiten der Datei.');
        }
    }


    private function processData($rows)
    {
        foreach ($rows as $index => $row) {
            // Kopfzeile überspringen
            if ($index == 0) continue;

            // Stelle sicher, dass mindestens 8 Spalten vorhanden sind
            if (count($row) < 8) {
                Log::warning("Zeile {$index} hat weniger als 8 Spalten: " . json_encode($row));
                continue;
            }

            // Excel-Spalten zuweisen (an Dokument anpassen)
            $locationId = trim($row[1]); // location_id (z. B. 4)
            $textType   = trim($row[2]); // text_type (z. B. Erlebnis)
            $category   = trim($row[3]); // Kategorie
            $uschrift   = trim($row[4]); // uschrift
            $text       = trim($row[5]); // text
            $addInfo    = trim($row[6]); // addinfo
            $is_active  = trim($row[7]); // anzeigen (1 oder 0)

            // Standort anhand location_id suchen
            $location = WwdeLocation::where('old_id', $locationId)->first();

//dd($location);

            if (!$location) {
                Log::warning("Kein Standort gefunden für location_id: {$locationId}");
                continue;
            }

            // updateOrCreate mit korrekten Spaltennamen
            ModLocationFilter::updateOrCreate(
                [
                    'location_id' => $location->id,
                    'text_type'   => $textType,
                    'uschrift'    => $uschrift,
                    'category'   => $category,
                    'is_active'   => ($is_active == 1 || $is_active === '1' || strtolower($is_active) === 'true'),
                    'addinfo'     => $addInfo ? $addInfo : null,
                ],
                [
                    'text' => $text,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }


}
