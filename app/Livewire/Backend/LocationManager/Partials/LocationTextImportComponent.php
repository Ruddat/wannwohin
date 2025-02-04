<?php

namespace App\Livewire\Backend\LocationManager\Partials;

use Livewire\Component;
use App\Models\WwdeLocation;
use App\Models\ModLocationFilter;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class LocationTextImportComponent extends Component
{
    use WithFileUploads;

    public $file;

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,csv|max:10240', // 10MB Limit
        ]);

        // Datei speichern, aber mit einem sicheren Namen
        $filename = time() . '-' . $this->file->getClientOriginalName();
        $path = $this->file->storeAs('uploads', $filename, 'local'); // Speichert in storage/app/uploads/

        if (!$path) {
            session()->flash('error', 'Datei konnte nicht gespeichert werden.');
            return;
        }

        // Absoluten Pfad korrekt ermitteln
        $fullPath = storage_path("app/uploads/{$filename}");

        if (!file_exists($fullPath)) {
            session()->flash('error', 'Gespeicherte Datei nicht gefunden: ' . $fullPath);
            return;
        }

        try {
            // Excel Datei laden
            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (!empty($rows)) {
                $this->processData($rows);
            }

            session()->flash('message', 'Datei erfolgreich importiert.');
        } catch (\Exception $e) {
            Log::error("Excel-Import Fehler: " . $e->getMessage());
            session()->flash('error', 'Fehler beim Verarbeiten der Datei.');
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

    public function render()
    {
        return view('livewire.backend.location-manager.partials.location-text-import-component');
    }
}
