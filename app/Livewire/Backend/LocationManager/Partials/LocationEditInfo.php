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

    // Debugging, um zu sehen, ob `countryId` verloren geht
   // dd("updatedContinentId:", "Erhaltener Continent ID:", $continentId, "Aktuelle Country ID:", $this->countryId, "Neue Länder:", $this->countries->pluck('id', 'title'));

    // Country-ID nur zurücksetzen, wenn sie nicht in der neuen Liste vorkommt
    if ($this->countryId && !$this->countries->pluck('id')->contains($this->countryId)) {
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

      //  dump('Before update', $location->toArray(), $data);

        // Manueller Fehler-Handler
        set_error_handler(function ($severity, $message, $file, $line) {
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        try {
            $location->update($data);
           // dump('After update', $location->fresh()->toArray());
            $this->dispatch('show-toast', type: 'success', message: 'Standortinformationen erfolgreich gespeichert.');
        } catch (\Throwable $e) { // Fängt Fehler und Exceptions
          //  dd('Error during update', $e->getMessage(), $e->getTraceAsString());
        } finally {
            restore_error_handler();
        }
    }

    public function fetchGeocodeData()
    {
        // Prüfen, ob ein Titel (Stadtname) vorhanden ist
        if (empty($this->title)) {
            $this->dispatch('show-toast', type: 'error', message: 'Bitte geben Sie zuerst einen Titel (Stadtname) ein.');
            return;
        }

        // GeocodeService aufrufen
        $geocodeService = new GeocodeService();
        $result = $geocodeService->searchByNominatimOnly($this->title);
//dd($result);
	//	dd("ISO2:", $this->iso2, "Gefundenes Land:", $this->countryId, "Verfügbare Länder:", $this->countries->pluck('id', 'title'));

        // Prüfen, ob ein gültiges Array zurückgegeben wurde
        if (!is_array($result) || empty($result)) {
            $this->dispatch('show-toast', type: 'error', message: 'Keine gültigen Geodaten gefunden. Bitte überprüfen Sie den Stadtnamen.');
            return;
        }

        // Sicherstellen, dass 'address' existiert und ein Array ist
        $address = $result['address'] ?? [];

        if (!is_array($address)) {
            $this->dispatch('show-toast', type: 'error', message: 'Ungültige Geodaten erhalten.');
            return;
        }

        // Felder mit den Ergebnissen füllen (mit zusätzlicher Absicherung)
        $this->iso2 = $address['country_code'] ?? null;
        $this->iso3 = $address['ISO3166-2-lvl4'] ?? null;
        $this->lat = $result['lat'] ?? null;
        $this->lon = $result['lon'] ?? null;
        $this->bundesstaatLong = $address['state'] ?? null;
        $this->bundesstaatShort = $address['county'] ?? null;
        $this->title = $address['city'] ?? $this->title; // Stadt automatisch setzen, falls leer
        $this->region = $address['region'] ?? null; // Falls eine Region existiert

        // Land anhand des country_codes aus der Datenbank suchen und setzen
        if (!empty($this->iso2)) {
            $country = WwdeCountry::where('country_code', strtoupper($this->iso2))->first();

            if ($country) {
                $this->countryId = $country->id;
                $this->continentId = $country->continent_id;
                //dd("Nach dem Setzen:", "Continent ID:", $this->continentId, "Country ID:", $this->countryId);

                $this->updatedContinentId($this->continentId);
                $this->countries = WwdeCountry::where('continent_id', $this->continentId)->get();
            }
        }
        $this->dispatch('updateCountrySelect', $this->countryId);
        // Erfolgsmeldung
        $this->dispatch('show-toast', type: 'success', message: 'Daten erfolgreich abgerufen und eingetragen.');
    }


    public function render()
    {
        return view('livewire.backend.location-manager.partials.location-edit-info');
    }
}
