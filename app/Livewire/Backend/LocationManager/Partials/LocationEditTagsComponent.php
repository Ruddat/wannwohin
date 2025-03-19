<?php

namespace App\Livewire\Backend\LocationManager\Partials;

use Livewire\Component;
use App\Models\WwdeLocation;

class LocationEditTagsComponent extends Component
{
    public $locationId;
    public $location;
    public $tags = [];
    public $best_traveltime = [];
    public $best_traveltime_text;
    public $text_sports;
    public $text_amusement_parks;
    public $travel_time_options;

    // Public Property für das Monats-Array
    public $months = [
        "January" => 1, "February" => 2, "March" => 3, "April" => 4,
        "May" => 5, "June" => 6, "July" => 7, "August" => 8,
        "September" => 9, "October" => 10, "November" => 11, "December" => 12
    ];

    public function mount($locationId)
    {
        $this->locationId = $locationId;
        $this->location = WwdeLocation::find($locationId);

        if ($this->location) {
            // Tags initialisieren
            $this->tags = [
                'list_beach' => $this->location->list_beach,
                'list_citytravel' => $this->location->list_citytravel,
                'list_sports' => $this->location->list_sports,
                'list_island' => $this->location->list_island,
                'list_culture' => $this->location->list_culture,
                'list_nature' => $this->location->list_nature,
                'list_watersport' => $this->location->list_watersport,
                'list_wintersport' => $this->location->list_wintersport,
                'list_mountainsport' => $this->location->list_mountainsport,
                'list_biking' => $this->location->list_biking,
                'list_fishing' => $this->location->list_fishing,
                'list_amusement_park' => $this->location->list_amusement_park,
                'list_water_park' => $this->location->list_water_park,
                'list_animal_park' => $this->location->list_animal_park,
            ];

        // Beste Reisezeit aus der Datenbank holen
        $best_traveltime_numbers = json_decode($this->location->best_traveltime_json, true) ?? [];

        // Zahlen in Monatsnamen umwandeln
        $this->best_traveltime = $this->convertNumbersToMonths($best_traveltime_numbers);

        // Weitere Initialisierungen
        $this->best_traveltime_text = $this->location->text_best_traveltime;
        $this->text_sports = $this->location->text_sports;
        $this->text_amusement_parks = $this->location->text_amusement_parks;
    }

    // UI-Optionen (Monatsnamen)
    $this->travel_time_options = array_keys($this->months);
}

    public function updateTags()
    {
        // Debugging: Zeige das aktuelle Array an
       // dd($this->best_traveltime);

        if ($this->location) {
            // Monatsnamen in Zahlen umwandeln
            $best_traveltime_numbers = $this->convertMonthsToNumbers($this->best_traveltime);

            // Null-Werte entfernen (falls ungültige Monatsnamen vorhanden waren)
            $best_traveltime_numbers = array_filter($best_traveltime_numbers, fn($value) => !is_null($value));

            // Zahlen sortieren
            sort($best_traveltime_numbers);

            // Daten speichern
            $this->location->update(array_merge($this->tags, [
                'best_traveltime_json' => json_encode($best_traveltime_numbers, JSON_UNESCAPED_UNICODE),
                'best_traveltime' => implode(' - ', [reset($best_traveltime_numbers), end($best_traveltime_numbers)]),
                'text_best_traveltime' => $this->cleanEditorContent($this->best_traveltime_text),
                'text_sports' => $this->cleanEditorContent($this->text_sports),
                'text_amusement_parks' => $this->cleanEditorContent($this->text_amusement_parks),
            ]));

            // Toast-Nachricht dispatchen
            $this->dispatch('show-toast', type: 'success', message: 'Tags & Reisezeiten erfolgreich aktualisiert!');
        }
    }

    protected function convertMonthsToNumbers(array $months): array
    {
        $monthMap = [
            "January" => 1, "February" => 2, "March" => 3, "April" => 4,
            "May" => 5, "June" => 6, "July" => 7, "August" => 8,
            "September" => 9, "October" => 10, "November" => 11, "December" => 12
        ];

        return array_map(function ($month) use ($monthMap) {
            return $monthMap[$month] ?? null; // Gibt die Zahl zurück oder null, falls der Monat ungültig ist
        }, $months);
    }

    protected function convertNumbersToMonths(array $numbers): array
    {
        $monthMap = [
            1 => "January", 2 => "February", 3 => "March", 4 => "April",
            5 => "May", 6 => "June", 7 => "July", 8 => "August",
            9 => "September", 10 => "October", 11 => "November", 12 => "December"
        ];

        return array_map(function ($number) use ($monthMap) {
            return $monthMap[$number] ?? null; // Gibt den Monatsnamen zurück oder null, falls die Zahl ungültig ist
        }, $numbers);
    }


/**
 * Prüft den Editor-Text und entfernt leere Inhalte wie "<p><br></p>".
 */
private function cleanEditorContent($content)
{
    // Entfernt Leerzeichen, HTML-Kommentare und überprüft, ob nur "<p><br></p>" o.ä. übrig bleibt.
    $cleaned = trim(strip_tags($content, '<img><a>')); // Erlaubt Bilder und Links

    return empty($cleaned) ? null : $content;
}



    public function render()
    {
        return view('livewire.backend.location-manager.partials.location-edit-tags-component');
    }
}
