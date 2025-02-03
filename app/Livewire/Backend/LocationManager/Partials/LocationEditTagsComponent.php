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

    public function mount($locationId)
    {
        $this->locationId = $locationId;
        $this->location = WwdeLocation::find($locationId);

        if ($this->location) {
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

            $this->best_traveltime = json_decode($this->location->best_traveltime_json, true) ?? [];
            sort($this->best_traveltime); // Sort the months alphabetically

            $this->best_traveltime_text = $this->location->text_best_traveltime;
            $this->text_sports = $this->location->text_sports;
            $this->text_amusement_parks = $this->location->text_amusement_parks;
        }

        $this->travel_time_options = [
            "January", "February", "March", "April", "May", "June", "July", "August", "September", "Oktober", "November", "December"
        ];
    }

    public function updateTags()
    {
        if ($this->location) {
            sort($this->best_traveltime); // Ensure sorting before saving

            $this->location->update(array_merge($this->tags, [
                'best_traveltime_json' => json_encode($this->best_traveltime, JSON_UNESCAPED_UNICODE),
                'best_traveltime' => implode(' - ', [reset($this->best_traveltime), end($this->best_traveltime)]),
                'text_best_traveltime' => $this->best_traveltime_text,
                'text_sports' => $this->text_sports,
                'text_amusement_parks' => $this->text_amusement_parks,
            ]));
            session()->flash('success', 'Tags successfully updated!');
        }
    }

    public function render()
    {
        return view('livewire.backend.location-manager.partials.location-edit-tags-component');
    }
}
