<?php

namespace App\Livewire\Backend\LocationManager\Partials;

use Livewire\Component;
use App\Models\WwdeLocation;

class LocationEditTexts extends Component
{
    public $locationId;
    public $pic1Text;
    public $pic2Text;
    public $pic3Text;
    public $textHeadline;
    public $textShort;
    public $textWhatToDo;
    public $textLocationClimate;
    public $textBestTravelTime;
    public $textSports;
    public $textAmusementParks;
    public $panoramaTextAndStyle;

    public function mount($locationId)
    {
        $location = WwdeLocation::findOrFail($locationId);

        $this->locationId = $locationId;
        $this->pic1Text = $location->pic1_text;
        $this->pic2Text = $location->pic2_text;
        $this->pic3Text = $location->pic3_text;
        $this->textHeadline = $location->text_headline;
        $this->textShort = $location->text_short;
        $this->textWhatToDo = $location->text_what_to_do;
        $this->textLocationClimate = $location->text_location_climate;
        $this->textBestTravelTime = $location->text_best_traveltime;
        $this->textSports = $location->text_sports;
        $this->textAmusementParks = $location->text_amusement_parks;
        $this->panoramaTextAndStyle = $location->panorama_text_and_style;
    }

    public function save()
    {
        $location = WwdeLocation::findOrFail($this->locationId);

        $location->update([
            'pic1_text' => $this->pic1Text,
            'pic2_text' => $this->pic2Text,
            'pic3_text' => $this->pic3Text,
            'text_headline' => $this->textHeadline,
            'text_short' => $this->textShort,
            'text_what_to_do' => $this->textWhatToDo,
            'text_location_climate' => $this->textLocationClimate,
            'text_best_traveltime' => $this->textBestTravelTime,
            'text_sports' => $this->textSports,
            'text_amusement_parks' => $this->textAmusementParks,
            'panorama_text_and_style' => $this->panoramaTextAndStyle,
        ]);

        session()->flash('success', 'Texte erfolgreich gespeichert.');
    }

    public function render()
    {
        return view('livewire.backend.location-manager.partials.location-edit-texts');
    }
}
