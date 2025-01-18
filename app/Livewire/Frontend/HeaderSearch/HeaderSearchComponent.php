<?php

namespace App\Livewire\Frontend\HeaderSearch;

use Livewire\Component;
use App\Models\WwdeLocation;

class HeaderSearchComponent extends Component
{
    public $searchTerm = '';
    public $suggestions = [];
    public $highlightedIndex = 0;

    /**
     * Aktualisiert die Vorschläge, wenn sich die Suchanfrage ändert.
     */
    public function updatedSearchTerm($value)
    {
        $this->suggestions = WwdeLocation::query()
            ->where('title', 'like', '%' . $value . '%')
            ->orWhere('alias', 'like', '%' . $value . '%')
            ->with(['country.continent']) // Beziehungen laden
            ->limit(10)
            ->get()
            ->map(function ($location) {
                return [
                    'title' => $location->title,
                    'alias' => $location->alias,
                    'country_alias' => $location->country->alias ?? null,
                    'continent_alias' => $location->country->continent->alias ?? null,
                ];
            })
            ->toArray();

        $this->highlightedIndex = 0; // Index zurücksetzen
    }
    /**
     * Pfeiltasten-Navigation: Auswahl nach oben/unten bewegen.
     */
    public function moveHighlight($direction)
    {
        if ($direction === 'up') {
            $this->highlightedIndex = max(0, $this->highlightedIndex - 1);
        } elseif ($direction === 'down') {
            $this->highlightedIndex = min(count($this->suggestions) - 1, $this->highlightedIndex + 1);
        }
    }

    /**
     * Suchanfrage ausführen.
     */
    public function search()
    {
//dd($this->suggestions);


        if (!empty($this->suggestions)) {
            $selectedSuggestion = $this->suggestions[$this->highlightedIndex];
            return redirect()->route('location.details', [
                'continent' => $selectedSuggestion['continent_alias'],
                'country' => $selectedSuggestion['country_alias'],
                'location' => $selectedSuggestion['alias'],
            ]);
        }
    }

    public function render()
    {
        return view('livewire.frontend.header-search.header-search-component');
    }
}
