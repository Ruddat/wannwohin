<?php

namespace App\Livewire\Backend\LocationManager\Partials;

use Livewire\Component;
use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use App\Models\WwdeContinent;
use App\Services\GeocodeService;

class LocationEditInfo extends Component
{
    public $locationId;
    public $continentId;
    public $countryId;
    public $iso2;
    public $iso3;
    public $title;
    public $alias;
    public $iataCode;
    public $flightHours;
    public $stopOver;
    public $distFromFRA;
    public $distType;
    public $lat;
    public $lon;
    public $stationId;
    public $bundesstaatLong;
    public $bundesstaatShort;
    public $noCityBut;
    public $population;
    public $continents = [];
    public $countries = [];
    public $finished;
    public $status;

    public function mount($locationId)
    {
        $this->continents = WwdeContinent::all();
        $this->countries = collect(); // Anfangs leer

        $location = WwdeLocation::findOrFail($locationId);

        $this->locationId = $locationId;
        $this->continentId = $location->continent_id;
        $this->countryId = $location->country_id;
        $this->iso2 = $location->iso2;
        $this->iso3 = $location->iso3;
        $this->title = $location->title;
        $this->alias = $location->alias;
        $this->iataCode = $location->iata_code;
        $this->flightHours = $location->flight_hours;
        $this->stopOver = $location->stop_over;
        $this->distFromFRA = $location->dist_from_FRA;
        $this->distType = $location->dist_type;
        $this->lat = $location->lat;
        $this->lon = $location->lon;
        $this->stationId = $location->station_id;
        $this->bundesstaatLong = $location->bundesstaat_long;
        $this->bundesstaatShort = $location->bundesstaat_short;
        $this->noCityBut = $location->no_city_but;
        $this->population = $location->population;
        $this->finished = $location->finished;
        $this->status = $location->status;

        // Länder des ausgewählten Kontinents laden, falls vorhanden
        if ($this->continentId) {
            $this->countries = WwdeCountry::where('continent_id', $this->continentId)->get();
        }
    }

    public function updatedContinentId($continentId)
    {
        $this->countries = WwdeCountry::where('continent_id', $continentId)->get();
        $this->countryId = null; // Zurücksetzen
    }

    public function save()
    {
        $this->validate([
            'continentId' => 'required|exists:wwde_continents,id',
            'countryId' => 'required|exists:wwde_countries,id',
            'iso2' => 'nullable|string|max:255',
            'iso3' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'iataCode' => 'nullable|string|max:255',
            'flightHours' => 'nullable|numeric',
            'stopOver' => 'nullable|integer',
            'distFromFRA' => 'nullable|integer',
            'distType' => 'nullable|string|max:255',
            'lat' => 'nullable|string|max:255',
            'lon' => 'nullable|string|max:255',
            'stationId' => 'nullable|string|max:255',
            'bundesstaatLong' => 'nullable|string|max:255',
            'bundesstaatShort' => 'nullable|string|max:255',
            'noCityBut' => 'nullable|string|max:255',
            'population' => 'nullable|integer',
            'status' => 'required|in:active,pending,inactive',
            'finished' => 'required|boolean',
        ]);

        $location = WwdeLocation::findOrFail($this->locationId);

        $location->update([
            'continent_id' => $this->continentId,
            'country_id' => $this->countryId,
            'iso2' => $this->iso2,
            'iso3' => $this->iso3,
            'title' => $this->title,
            'alias' => $this->alias,
            'iata_code' => $this->iataCode,
            'flight_hours' => $this->flightHours,
            'stop_over' => $this->stopOver,
            'dist_from_FRA' => $this->distFromFRA,
            'dist_type' => $this->distType,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'lat_new' => $this->lat,
            'lon_new' => $this->lon,
            'station_id' => $this->stationId,
            'bundesstaat_long' => $this->bundesstaatLong,
            'bundesstaat_short' => $this->bundesstaatShort,
            'no_city_but' => $this->noCityBut,
            'population' => $this->population,
            'status' => $this->status,
            'finished' => $this->finished,
        ]);

        session()->flash('success', 'Standortinformationen erfolgreich gespeichert.');
    }

    public function fetchGeocodeData()
    {
        // Prüfen, ob ein Titel (Stadtname) vorhanden ist
        if (empty($this->title)) {
            session()->flash('error', 'Bitte geben Sie zuerst einen Titel (Stadtname) ein.');
            return;
        }

        // GeocodeService aufrufen
        $geocodeService = new GeocodeService();
        $result = $geocodeService->searchByNominatimOnly($this->title);

        if (!empty($result)) {
            // Felder mit den Ergebnissen füllen
            $this->iso2 = $result['address']['country_code'] ?? null;
            $this->iso3 = $result['address']['ISO3166-2-lvl4'] ?? null;
            $this->lat = $result['lat'] ?? null;
            $this->lon = $result['lon'] ?? null;
            $this->bundesstaatLong = $result['address']['state'] ?? null;
            $this->bundesstaatShort = $result['address']['county'] ?? null; // Landkreis als "Kurzversion"

            // Land anhand des country_codes aus der Datenbank suchen und setzen
            $country = WwdeCountry::where('country_code', strtoupper($this->iso2))->first();
            if ($country) {
                $this->countryId = $country->id;
                $this->continentId = $country->continent_id;
                $this->updatedContinentId($this->continentId); // Länderliste aktualisieren
            }

            session()->flash('success', 'Daten erfolgreich abgerufen und eingetragen.');
        } else {
            session()->flash('error', 'Keine Daten gefunden. Bitte überprüfen Sie den Stadtnamen.');
        }
    }










    public function render()
    {
        return view('livewire.backend.location-manager.partials.location-edit-info');
    }
}
