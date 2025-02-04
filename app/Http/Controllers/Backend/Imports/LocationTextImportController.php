<?php

namespace App\Http\Controllers\Backend\Imports;

use Exception;
use App\Models\WwdeLocation;
use Illuminate\Http\Request;
use App\Models\ModLocationFilter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LocationTextImportController extends Controller
{
    public function index()
    {
        dd('LocationTextImportController@index()');
        
        return view('backend.imports.location-text-import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:10240',
        ]);

        $file = $request->file('file');
        $filename = time() . '-' . $file->getClientOriginalName();
        $path = $file->storeAs('uploads', $filename, 'local'); // Speichert in storage/app/uploads/

        if (!$path) {
            return back()->with('error', 'Datei konnte nicht gespeichert werden.');
        }

        $fullPath = storage_path("app/uploads/{$filename}");

        if (!file_exists($fullPath)) {
            return back()->with('error', 'Gespeicherte Datei nicht gefunden: ' . $fullPath);
        }

        try {
            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (!empty($rows)) {
                $this->processData($rows);
            }

            return back()->with('message', 'Datei erfolgreich importiert.');
        } catch (Exception $e) {
            Log::error("Excel-Import Fehler: " . $e->getMessage());
            return back()->with('error', 'Fehler beim Verarbeiten der Datei.');
        }
    }

    private function processData($rows)
    {
        foreach ($rows as $index => $row) {
            if ($index == 0) continue; // Kopfzeile überspringen
            if (count($row) < 5) continue; // Ungültige Zeilen überspringen

            $locationTitle = trim($row[1]); // Standort-Name oder ID
            $textType = trim($row[2]);
            $uschrift = trim($row[3]);
            $text = trim($row[4]);

            // Standort-ID ermitteln
            $location = WwdeLocation::where('title', $locationTitle)
                ->orWhere('alias', $locationTitle)
                ->first();

            if (!$location) {
                Log::warning("Kein Standort gefunden für: {$locationTitle}");
                continue;
            }

            // Daten speichern oder aktualisieren
            ModLocationFilter::updateOrCreate(
                [
                    'location_id' => $location->id,
                    'text_type' => $textType,
                    'uschrift' => $uschrift,
                ],
                ['text' => $text]
            );
        }
    }
}
