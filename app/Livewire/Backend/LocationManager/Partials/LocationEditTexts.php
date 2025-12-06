<?php

namespace App\Livewire\Backend\LocationManager\Partials;

use Livewire\Component;
use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use Illuminate\Support\Facades\Cache;

class LocationEditTexts extends Component
{
    public $locationId;
    public $locationTitle;
    public $textPic1;
    public $textPic2;
    public $textPic3;
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
    public $panoramaTitle;
    public $panoramaShortText;
    public $imageShortText; // New field for short text under the image

    public function mount($locationId)
    {
        $location = WwdeLocation::findOrFail($locationId);

        $this->locationId = $locationId;
        $this->locationTitle = $location->title;
        $this->textPic1 = $location->text_pic2; // Bild 2 Text (Faktencheck)
        $this->textPic2 = $location->text_pic1; // Bild 3 Text (Faktencheck)
        $this->textPic3 = $location->text_pic3; // Bild 4 Text (Faktencheck)
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
        $this->panoramaTitle = $location->panorama_title;
        $this->panoramaShortText = $location->panorama_short_text;
        $this->imageShortText = $location->image_short_text; // Load the new field
    }

    public function save()
    {
        $location = WwdeLocation::findOrFail($this->locationId);



        $location->update([
            'pic1_text' => $this->cleanEditorContent($this->pic1Text),
            'pic2_text' => $this->cleanEditorContent($this->pic2Text),
            'pic3_text' => $this->cleanEditorContent($this->pic3Text),
            'text_headline' => $this->cleanEditorContent($this->textHeadline),
            'text_short' => $this->cleanEditorContent($this->textShort),
            'text_what_to_do' => $this->cleanEditorContent($this->textWhatToDo),
            'text_location_climate' => $this->cleanEditorContent($this->textLocationClimate),
            'panorama_text_and_style' => $this->cleanEditorContent($this->panoramaTextAndStyle),
            'panorama_title' => $this->cleanEditorContent($this->panoramaTitle),
            'panorama_short_text' => $this->cleanEditorContent($this->panoramaShortText),
            'image_short_text' => $this->cleanEditorContent($this->imageShortText),
        ]);

        Cache::forget("location_texts_{$this->locationId}");

        if ($location->country_id) {
            Cache::forget("locations_{$location->country_id}");
            $country = WwdeCountry::find($location->country_id);
            if ($country) {
                Cache::forget("country_{$country->alias}");
                if ($country->continent_id) {
                    Cache::forget("countries_{$country->continent_id}");
                }
            }
        }

        $this->dispatch('show-toast', type: 'success', message: 'Texte erfolgreich gespeichert.');
    }

    private function cleanEditorContent($content)
    {
        $allowedTags = '<a><img><b><strong><i><em><u><ul><ol><li><br><p><h1><h2><h3><h4><h5><h6><span>';
        $cleaned = trim(strip_tags($content, $allowedTags));

        return empty($cleaned) ? null : $cleaned;
    }

    public function render()
    {
        return view('livewire.backend.location-manager.partials.location-edit-texts', [
            'locationTitle' => $this->locationTitle
        ]);
    }
}
