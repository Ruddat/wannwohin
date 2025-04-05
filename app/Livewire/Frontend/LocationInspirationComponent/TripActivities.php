<?php

namespace App\Livewire\Frontend\LocationInspirationComponent;

use Livewire\Component;
use App\Models\ModLocationFilter;

class TripActivities extends Component
{
    public int $locationId;
    public string $locationTitle;
    public string $selectedActivity = '';

    public function selectActivity(string $id)
    {
        $this->selectedActivity = $this->selectedActivity === $id ? '' : $id;
    }

    public function addToTrip(string $id)
    {
        $this->dispatch('activityAddedToTrip', $id);
        session()->flash('success', 'Zur Reise hinzugefügt!');
    }

    public function getActivitiesProperty()
    {
        return ModLocationFilter::where('location_id', $this->locationId)
            ->where('is_active', 1)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => 'activity-' . $item->id,
                    'title' => $item->uschrift,
                    'description' => $item->text,
                    'category' => strtolower($item->text_type),
                    'icon' => match (strtolower($item->text_type)) {
                        'erlebnis' => 'fa-city',
                        'sport' => 'fa-person-running',
                        'freizeitpark' => 'fa-tree',
                        default => 'fa-location-dot',
                    },
                    'duration' => ['1 Stunde', '2–3 Stunden', 'Halbtags'][rand(0, 2)],
                    'season' => ['Frühling', 'Sommer', 'Ganzjährig'][rand(0, 2)],
                    'rating' => rand(85, 99) . '% positiv',
                    'isRecommended' => rand(0, 1) === 1,
                ];
            });
    }


    public function render()
    {
        return view('livewire.frontend.location-inspiration-component.trip-activities');
    }
}
