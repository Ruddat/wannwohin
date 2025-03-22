<?php

namespace App\Livewire\Frontend\HeaderSearch;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;
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
        $cacheKey = 'search_' . md5($value);

        // Vorschläge aus dem Cache abrufen oder neu generieren
        $this->suggestions = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($value) {
            return WwdeLocation::query()
                ->where('status', 'active')
                ->where('finished', 1)
                ->where(function ($query) use ($value) {
                    $query->where('title', 'like', '%' . $value . '%')
                          ->orWhere('alias', 'like', '%' . $value . '%')
                          ->orWhereHas('country', function ($countryQuery) use ($value) {
                              $countryQuery->where('title', 'like', '%' . $value . '%')
                                           ->orWhere('alias', 'like', '%' . $value . '%');
                          })
                          ->orWhereHas('country.continent', function ($continentQuery) use ($value) {
                              $continentQuery->where('title', 'like', '%' . $value . '%')
                                            ->orWhere('alias', 'like', '%' . $value . '%');
                          });
                })
                ->with(['country.continent'])
                ->limit(10)
                ->get()
                ->map(function ($location) {
                    return [
                        'title' => $location->title,
                        'alias' => $location->alias,
                        'country_title' => $location->country->title ?? null,
                        'country_alias' => $location->country->alias ?? null,
                        'continent_title' => $location->country->continent->title ?? null,
                        'continent_alias' => $location->country->continent->alias ?? null,
                    ];
                })
                ->toArray();
        });

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
        if (!empty($this->suggestions)) {
            $selectedSuggestion = $this->suggestions[$this->highlightedIndex];
            return redirect()->route('location.details', [
                'continent' => $selectedSuggestion['continent_alias'],
                'country' => $selectedSuggestion['country_alias'],
                'location' => $selectedSuggestion['alias'],
            ]);
        }
    }

    /**
     * Wird ausgelöst, wenn der Benutzer auf ein Suchergebnis klickt.
     */
    public function selectSuggestion($index)
    {
        if (isset($this->suggestions[$index])) {
            $this->highlightedIndex = $index;
            $this->search();
        }
    }

    public function render()
    {
        return view('livewire.frontend.header-search.header-search-component');
    }
}
