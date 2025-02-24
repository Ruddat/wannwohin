<?php

namespace App\Livewire\Frontend\TravelExplorer;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\WwdeLocation;
use Livewire\WithPagination;
use App\Models\WwdeContinent;
use App\Models\ModLocationFilter;
use Illuminate\Support\Facades\Cache;

class TravelExplorerComponent extends Component
{
    use WithPagination;

    public $selectedMonth = null;
    public $selectedActivities = [];
    public $selectedContinent = null;
    public $perPage = 12;
    public $loading = false;

    protected $months = [
        1 => 'Januar', 2 => 'Februar', 3 => 'März', 4 => 'April', 5 => 'Mai', 6 => 'Juni',
        7 => 'Juli', 8 => 'August', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Dezember'
    ];

    protected $activityMap = [
        'list_beach' => 'Strandurlaub',
        'list_citytravel' => 'Städtereise',
        'list_sports' => 'Sporturlaub',
        'list_island' => 'Inselurlaub',
        'list_culture' => 'Kulturreise',
        'list_nature' => 'Natururlaub',
        'list_watersport' => 'Wassersport',
        'list_wintersport' => 'Wintersport',
        'list_mountainsport' => 'Bergsport',
        'list_biking' => 'Fahrradurlaub',
        'list_fishing' => 'Angelurlaub',
        'list_amusement_park' => 'Freizeitpark',
        'list_water_park' => 'Wasserpark',
        'list_animal_park' => 'Tierpark',
    ];

    protected $categoryMap = [
        'erlebnis' => ['color' => '#9c27b0', 'icon' => 'fas fa-arrow-right', 'label' => 'Erlebnis'],
        'sport' => ['color' => '#4caf50', 'icon' => 'fas fa-running', 'label' => 'Sport'],
        'freizeitpark' => ['color' => '#e91e63', 'icon' => 'fas fa-ticket-alt', 'label' => 'Freizeitpark'],
        'inspiration' => ['color' => '#2196f3', 'icon' => 'fas fa-lightbulb', 'label' => 'Inspiration'],
        'wetter' => ['color' => '#fbc02d', 'icon' => 'fas fa-sun', 'label' => 'Feuchter'],
    ];






    public function updated($propertyName)
    {
        $this->loading = true;
        $this->resetPage();
    }

    public function render()
    {
        $this->loading = true;

        // Cache statische Daten
        $categories = Cache::remember('travel_categories', 60 * 60, fn() =>
            ModLocationFilter::select('text_type')
                ->distinct()
                ->pluck('text_type')
                ->mapWithKeys(fn($type) => [Str::lower($type) => ucfirst($type)]) // Kleinbuchstaben für Konsistenz
                ->toArray()
        );

        // Hole die Filtertexte für jede Location
        $locationTexts = Cache::remember('location_texts', 60 * 60, fn() =>
            ModLocationFilter::select('location_id', 'text_type', 'uschrift', 'text')
                ->get()
                ->groupBy('location_id') // Gruppiere nach Location
                ->map(function ($items) {
                    return $items->map(function ($item) {
                        return [
                            'type' => $item->text_type,
                            'title' => $item->uschrift,
                            'description' => $item->text,
                        ];
                    });
                })
                ->toArray()
        );

        // Rest des Codes bleibt unverändert...
        $continents = Cache::remember('travel_continents', 60 * 60, fn() =>
            WwdeContinent::pluck('title', 'id')->toArray()
        );

        // Optimierte Abfrage mit Pagination
        $locations = WwdeLocation::query()
        ->where('status', 'active')
        ->where('finished', 1)
        ->when($this->selectedMonth, fn($query) =>
            $query->whereRaw('JSON_CONTAINS(best_traveltime_json, ?)', [json_encode((int)$this->selectedMonth)])
        )
        ->when($this->selectedActivities, fn($query) =>
            $query->where(function ($q) {
                foreach ($this->selectedActivities as $activity) {
                    $q->orWhere($activity, 1);
                }
            })
        )
        ->when($this->selectedContinent, fn($query) =>
            $query->where('continent_id', $this->selectedContinent)
        )
        ->select('id', 'title', 'alias', 'country_id', 'best_traveltime_json', 'text_pic1', 'text_pic2', 'text_pic3')
        ->with(['country' => fn($q) => $q->select('id', 'continent_id', 'alias')])
        ->orderBy('title')
        ->paginate($this->perPage);

        // Zählen der Vorschläge pro Kategorie
        $suggestions = [];
        foreach ($this->categoryMap as $key => $data) {
            if ($key !== 'wetter') { // Wetter-Kategorie separat handhaben
                $suggestions[Str::lower($data['label'])] = WwdeLocation::where('status', 'active')
                    ->where('finished', 1)
                    ->whereHas('filters', function ($query) use ($key) {
                        $query->where('text_type', $key);
                    })
                    ->when($this->selectedMonth, fn($query) =>
                        $query->whereRaw('JSON_CONTAINS(best_traveltime_json, ?)', [json_encode((int)$this->selectedMonth)])
                    )
                    ->when($this->selectedActivities, fn($query) =>
                        $query->where(function ($q) {
                            foreach ($this->selectedActivities as $activity) {
                                $q->orWhere($activity, 1);
                            }
                        })
                    )
                    ->when($this->selectedContinent, fn($query) =>
                        $query->where('continent_id', $this->selectedContinent)
                    )
                    ->count();
            }
        }

        // Wetter-Daten (statisch für Demo, dynamisch über API anpassbar)
        $weather = ['title' => 'Feuchter', 'temp' => '22°C'];

        $this->loading = false;

        return view('livewire.frontend.travel-explorer.travel-explorer-component', [
            'locations' => $locations,
            'categories' => $categories,
            'months' => $this->months,
            'activityMap' => $this->activityMap,
            'continents' => $continents,
            'suggestions' => $suggestions,
            'weather' => $weather,
            'categoryMap' => $this->categoryMap,
            'locationTexts' => $locationTexts, // Filtertexte für Locations an die View übergeben
        ]);
    }

    public function resetFilters()
    {
        $this->reset(['selectedMonth', 'selectedActivities', 'selectedContinent']);
        $this->resetPage();
    }
}
