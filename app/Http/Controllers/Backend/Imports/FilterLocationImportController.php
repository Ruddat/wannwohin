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
            if ($index == 0) continue; // Kopfzeile überspringen
            if (count($row) < 5) continue; // Ungültige Zeilen überspringen

            $oldId = trim($row[1]); // Alte Standort-ID aus der Excel-Tabelle
            $textType = trim($row[2]);
            $uschrift = trim($row[3]);
            $text = trim($row[4]);

            // Standort anhand der `old_id` suchen
            $location = WwdeLocation::where('old_id', $oldId)->first();

            if (!$location) {
                Log::warning("Kein Standort gefunden für OLD_ID: {$oldId}");
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
