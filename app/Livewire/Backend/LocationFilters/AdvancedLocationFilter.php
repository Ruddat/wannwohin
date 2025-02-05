<?php

namespace App\Livewire\Backend\LocationFilters;

use Livewire\Component;
use App\Models\WwdeLocation;
use App\Models\ModLocationFilter;
use Illuminate\Support\Facades\Cache;

class AdvancedLocationFilter extends Component
{
    public $selectedCategories = [];
    public $searchQuery = '';
    public $selectedUschriften = [];
    public $filteredUschriften = [];
    public $filteredLocations = [];
    public $selectedLocation = null;

    public function updatedSelectedCategories()
    {
        $this->filteredUschriften = $this->getFilteredUschriften();
    }

    public function updatedSearchQuery()
    {
        $this->filteredUschriften = $this->getFilteredUschriften();
    }

    protected function getFilteredUschriften()
    {
        if (!empty($this->selectedCategories)) {
            return ModLocationFilter::whereIn('text_type', $this->selectedCategories)
                ->when($this->searchQuery, function ($query) {
                    $query->where('uschrift', 'like', '%' . $this->searchQuery . '%');
                })
                ->select('uschrift')
                ->distinct()
                ->orderBy('uschrift')
                ->pluck('uschrift')
                ->toArray();
        }
        return [];
    }

    public function selectUschrift($uschrift)
    {
        if (!in_array($uschrift, $this->selectedUschriften)) {
            $this->selectedUschriften[] = $uschrift;
        }
        $this->searchQuery = '';
    }

    public function removeUschrift($uschrift)
    {
        $this->selectedUschriften = array_diff($this->selectedUschriften, [$uschrift]);
        $this->updatedSelectedUschriften();
    }

    public function updatedSelectedUschriften()
    {
        $this->filteredLocations = $this->getFilteredLocations();
    }

    protected function getFilteredLocations()
    {
        if (!empty($this->selectedUschriften)) {
            return WwdeLocation::whereHas('filters', function ($query) {
                $query->whereIn('uschrift', $this->selectedUschriften);
            })->get();
        }
        return [];
    }

    public function showLocationDetails($locationId)
    {
        $this->selectedLocation = WwdeLocation::with('filters')->find($locationId);
    }

    public function render()
    {
        $categories = Cache::remember('mod_location_categories', now()->addMinutes(30), function () {
            return ModLocationFilter::distinct()->pluck('text_type')->toArray();
        });

        return view('livewire.backend.location-filters.advanced-location-filter', [
            'categories' => $categories
        ])->layout('backend.layouts.livewiere-main');
    }
}
