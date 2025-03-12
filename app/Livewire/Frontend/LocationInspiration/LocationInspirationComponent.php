<?php

namespace App\Livewire\Frontend\LocationInspiration;

use Livewire\Component;
use App\Models\ModLocationFilter;

class LocationInspirationComponent extends Component
{
    public $locationId;
    public $locationTitle;
    public $suggestions = [];

    public $selected = null;        // Aktuell ausgewählte Inspiration (bei Unterkategorie)
    public $selectedType = null;    // Gewählter Haupttyp (Sport, Erlebnis, Freizeitpark)
    public $categories = [];        // Unterkategorien für den ausgewählten Haupttyp

    public $randomSuggestions = []; // Drei zufällige Vorschläge
    public $randomMode = false;     // Wenn true, sind wir im Random‑Mode

    public $tripPlan = [];

    public function mount($locationId, $locationTitle)
    {
        $this->locationId = $locationId;
        $this->locationTitle = $locationTitle;

        $this->suggestions = ModLocationFilter::where('location_id', $locationId)
            ->where('is_active', 1)
            ->get()
            ->groupBy('text_type')
            ->toArray();

        $this->tripPlan = session('trip_plan', []);
    }

    /**
     * Beim Klick auf eine der Haupt-Kacheln: zeige die Unterkategorien.
     */
    public function showCategories($type)
    {
        $this->randomMode = false; // falls vorher Random aktiv war
        $this->selectedType = $type;
        $this->categories = ModLocationFilter::where('location_id', $this->locationId)
            ->where('is_active', 1)
            ->where('text_type', $type)
            ->pluck('category')
            ->unique()
            ->values()
            ->toArray();
        $this->selected = null;
    }

    /**
     * Zeigt eine zufällige Inspiration aus der gewählten Unterkategorie an.
     */
    public function showCategorySuggestion($category)
    {
        $items = ModLocationFilter::where('location_id', $this->locationId)
            ->where('is_active', 1)
            ->where('category', $category)
            ->get()
            ->toArray();

        if (count($items) > 0) {
            $this->selected = $items[array_rand($items)];
            // Hier könnte man auch ein Event dispatchen, falls Animationen via Alpine.js gewünscht sind
        }
    }

    /**
     * Wechselt in den Random‑Mode: zeigt drei zufällige Vorschläge und blendet die Hauptkacheln aus.
     */
    public function randomInspiration()
    {
        $this->randomMode = true;
        // Schließe die Unterkategorien:
        $this->selectedType = null;
        $this->categories = [];

        $all = [];
        foreach ($this->suggestions as $group) {
            $all = array_merge($all, $group);
        }
        if (count($all) === 0) {
            $this->randomSuggestions = [];
            return;
        }

        // Gewichte basierend auf Tripplan (optional)
        $weights = collect($this->tripPlan)->groupBy('text_type')->map->count()->toArray();
        $weightedSuggestions = collect($all)->map(function ($item) use ($weights) {
            $item['weight'] = $weights[$item['text_type']] ?? 0;
            return $item;
        })->sortByDesc('weight')->values();

        $this->randomSuggestions = $weightedSuggestions->take(3)->toArray();
    }

    /**
     * Setzt den Zustand zurück: Hauptkacheln und Unterkategorien werden wieder angezeigt.
     */
    public function resetSelection()
    {
        $this->selectedType = null;
        $this->categories = [];
        $this->selected = null;
        $this->randomSuggestions = [];
        $this->randomMode = false;
    }

    public function removeFromTripPlan($suggestionId)
    {

        $this->tripPlan = collect($this->tripPlan)
        ->reject(function($item) use ($suggestionId) {
            return $item['id'] == $suggestionId;
        })
        ->values()
        ->toArray();
        session()->put('trip_plan', $this->tripPlan);
    }

    /**
     * Fügt einen Vorschlag dem Tripplan hinzu und speichert diesen in der Session.
     */
    public function addToTripPlan($suggestionId)
    {
        $suggestion = ModLocationFilter::find($suggestionId);
        if ($suggestion && !collect($this->tripPlan)->contains('id', $suggestion->id)) {
            $suggestionArray = $suggestion->toArray();
            $suggestionArray['estimated_time'] = rand(1, 3) . ' Stunden'; // Platzhalter
            $this->tripPlan[] = $suggestionArray;
            session()->put('trip_plan', $this->tripPlan);
        }
    }

    public function render()
    {
        return view('livewire.frontend.location-inspiration.location-inspiration-component');
    }
}
