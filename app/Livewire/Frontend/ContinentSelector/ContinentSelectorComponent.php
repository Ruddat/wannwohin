<?php

namespace App\Livewire\Frontend\ContinentSelector;

use Livewire\Component;
use App\Models\WwdeContinent;

class ContinentSelectorComponent extends Component
{
    public $continentId;

    public function updatedContinentId($continentId)
    {
        $this->continentId = $continentId;

        // Finde den Alias des ausgewählten Kontinents
        $continent = WwdeContinent::find($this->continentId);

        // Weiterleitung zur Route mit dem Alias
        return redirect()->route('continent.countries', ['continentAlias' => $continent->alias]);
    }

    public function render()
    {
        return view('livewire.frontend.continent-selector.continent-selector-component', [
            'continents' => $this->getContinents(),
        ]);
    }

    private function getContinents()
    {
        // Nur Kontinente mit Status "active" laden
        return WwdeContinent::select('id', 'title')
            ->where('status', 'active')
            ->orderBy('title')
            ->get();
    }
}
