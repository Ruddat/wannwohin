<?php

namespace App\Livewire\Backend\LocationManager\Partials;

use Livewire\Component;
use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use App\Models\WwdeContinent;
use App\Services\GeocodeService;
use Illuminate\Support\Facades\Log;

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
    public $region;
    public $continents = [];
    public $countries;
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

    if (
        $this->countryId &&
        $this->countries instanceof \Illuminate\Support\Collection &&
        !$this->countries->pluck('id')->contains($this->countryId)
    ) {
        $this->countryId = null;
    }
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
        'lat' => 'nullable|numeric',
        'lon' => 'nullable|numeric',
        'stationId' => 'nullable|string|max:255',
        'bundesstaatLong' => 'nullable|string|max:255',
        'bundesstaatShort' => 'nullable|string|max:255',
        'noCityBut' => 'nullable|string|max:255',
        'population' => 'nullable|integer',
        'status' => 'required|in:active,pending,inactive',
        'finished' => 'required|boolean',
    ]);

    $location = WwdeLocation::findOrFail($this->locationId);

    $data = [
        'continent_id' => $this->continentId ?? 0,
        'country_id' => $this->countryId ?? 0,
        'iso2' => $this->iso2 ?? '',
        'iso3' => $this->iso3 ?? '',
        'title' => $this->title ?? 'Unbekannt',
        'alias' => $this->alias ?? '',
        'iata_code' => $this->iataCode ?? '',
        'flight_hours' => $this->flightHours ?? 0,
        'stop_over' => $this->stopOver ?? 0,
        'dist_from_FRA' => $this->distFromFRA ?? 0,
        'dist_type' => $this->distType ?? '',
        'lat' => $this->lat ?? null,
        'lon' => $this->lon ?? null,
        'lat_new' => $this->lat ?? null,
        'lon_new' => $this->lon ?? null,
        'station_id' => $this->stationId ?? '',
        'bundesstaat_long' => $this->bundesstaatLong ?? '',
        'bundesstaat_short' => $this->bundesstaatShort ?? '',
        'no_city_but' => $this->noCityBut ?? '',
        'population' => $this->population ?? 0,
        'status' => $this->status ?? 'pending',
        'finished' => $this->finished ?? 0,
    ];

    Log::info('Saving location', $data);

    try {
        $location->update($data);
    } catch (\Throwable $e) {
        Log::error("Location update failed: " . $e->getMessage());
        $this->dispatch('show-toast', type: 'error', message: 'Fehler: ' . $e->getMessage());
        return;
    }

    $this->dispatch('show-toast', type: 'success', message: 'Standortinformationen erfolgreich gespeichert.');
}


public function fetchGeocodeData()
{
    if (empty($this->title)) {
        $this->dispatch('show-toast', type: 'error', message: 'Bitte geben Sie zuerst einen Titel ein.');
        return;
    }

    $geocodeService = new GeocodeService();
    $result = $geocodeService->searchByNominatimOnly($this->title);

    if (!is_array($result) || empty($result)) {
        $this->dispatch('show-toast', type: 'error', message: 'Keine gültigen Geodaten gefunden.');
        return;
    }

    $address = $result['address'] ?? [];

    // Daten übernehmen
    $this->iso2 = $address['country_code'] ?? null;
    $this->iso3 = $address['ISO3166-2-lvl4'] ?? null;
    $this->lat = $result['lat'] ?? null;
    $this->lon = $result['lon'] ?? null;
    $this->bundesstaatLong = $address['state'] ?? null;
    $this->bundesstaatShort = $address['county'] ?? null;
    $this->title = $address['city'] ?? $this->title;
    $this->region = $address['region'] ?? null;

    // Kontinent + Land sicher setzen
    if (!empty($this->iso2)) {
        $country = WwdeCountry::where('country_code', strtoupper($this->iso2))->first();

        if ($country) {
            $this->continentId = $country->continent_id;
            $this->countries = WwdeCountry::where('continent_id', $this->continentId)->get();
            $this->countryId = $country->id;
        }
    }

    if (!$this->continentId || !$this->countryId) {
        $this->dispatch('show-toast', type: 'error', message: 'Kontinent oder Land konnte nicht automatisch gesetzt werden.');
        return;
    }

    $this->save();

    $this->dispatch('show-toast', type: 'success', message: 'Geodaten aktualisiert & gespeichert.');
}





    public function render()
    {
        return view('livewire.backend.location-manager.partials.location-edit-info');
    }
}
