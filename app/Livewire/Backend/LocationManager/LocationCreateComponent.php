<?php

namespace App\Livewire\Backend\LocationManager;

use Livewire\Component;
use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use App\Models\WwdeContinent;
use App\Services\GeocodeService;

class LocationCreateComponent extends Component
{
    public $continentId, $countryId, $iso2, $iso3, $title, $alias, $iataCode, $flightHours, $stopOver,
           $distFromFRA, $distType, $lat, $lon, $stationId, $bundesstaatLong, $bundesstaatShort,
           $noCityBut, $population, $finished = 0, $status = 'pending';

    public $continents = [], $countries = [];

    public $showCreateModal = false;

    protected $listeners = ['openCreateModal' => 'openModal'];

    public function mount()
    {
        $this->continents = WwdeContinent::all();
        $this->countries = collect();
    }

    public function openModal()
    {
        $this->reset(); // vollständig zurücksetzen
        $this->continents = WwdeContinent::all(); // Kontinente erneut laden
        $this->countries = collect();
        $this->showCreateModal = true;
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
    }

    public function updatedContinentId($continentId)
    {
        $this->countries = WwdeCountry::where('continent_id', $continentId)->get();

        if ($this->countryId && !$this->countries->pluck('id')->contains($this->countryId)) {
            $this->countryId = null;
        }
    }

    public function save()
    {
        $this->resetValidation(); // vorherige Fehler zurücksetzen

        try {
            $validatedData = $this->validate([
                'continentId'       => 'required|exists:wwde_continents,id',
                'countryId'         => 'required|exists:wwde_countries,id',
                'title'             => 'required|string|max:255|unique:wwde_locations,title',
                'alias'             => 'nullable|string|max:255|unique:wwde_locations,alias',
                'iso2'              => 'nullable|string|max:2',
                'iso3'              => 'nullable|string|max:5',
                'iataCode'          => 'nullable|string|max:10',
                'flightHours'       => 'nullable|numeric',
                'stopOver'          => 'nullable|integer',
                'distFromFRA'       => 'nullable|integer',
                'distType'          => 'nullable|string|max:50',
                'lat'               => 'nullable|string',
                'lon'               => 'nullable|string',
                'stationId'         => 'nullable|string|max:50',
                'bundesstaatLong'   => 'nullable|string|max:100',
                'bundesstaatShort'  => 'nullable|string|max:50',
                'noCityBut'         => 'nullable|string|max:255',
                'population'        => 'nullable|integer',
                'status'            => 'required|in:active,pending,inactive',
                'finished'          => 'required|boolean',
            ]);

            WwdeLocation::create([
                'continent_id'      => $validatedData['continentId'],
                'country_id'        => $validatedData['countryId'],
                'title'             => $validatedData['title'],
                'alias'             => $validatedData['alias'] ?? null,
                'iso2'              => $validatedData['iso2'] ?? null,
                'iso3'              => $validatedData['iso3'] ?? null,
                'iata_code'         => $validatedData['iataCode'] ?? null,
                'flight_hours'      => $validatedData['flightHours'] ?? null,
                'stop_over'         => $validatedData['stopOver'] ?? null,
                'dist_from_FRA'     => $validatedData['distFromFRA'] ?? null,
                'dist_type'         => $validatedData['distType'] ?? null,
                'lat'               => $validatedData['lat'] ?? null,
                'lon'               => $validatedData['lon'] ?? null,
                'lat_new'           => $validatedData['lat'] ?? null,
                'lon_new'           => $validatedData['lon'] ?? null,
                'station_id'        => $validatedData['stationId'] ?? null,
                'bundesstaat_long'  => $validatedData['bundesstaatLong'] ?? null,
                'bundesstaat_short' => $validatedData['bundesstaatShort'] ?? null,
                'no_city_but'       => $validatedData['noCityBut'] ?? null,
                'population'        => $validatedData['population'] ?? null,
                'status'            => $validatedData['status'],
                'finished'          => $validatedData['finished'],
            ]);

            $this->closeModal();
            $this->dispatch('refreshLocations');
            $this->dispatch('show-toast', type: 'success', message: 'Standort erfolgreich erstellt.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validierungsfehler elegant als Meldungen an das Template übergeben
            $this->setErrorBag($e->validator->getMessageBag());
        } catch (\Exception $e) {
            // Sonstige Fehler per Toast anzeigen
            $this->dispatch('show-toast', type: 'error', message: $e->getMessage());
        }
    }



    public function fetchGeocodeData()
    {
        if (empty($this->title)) {
            $this->dispatch('show-toast', type: 'error', message: 'Bitte zuerst einen Titel eingeben.');
            return;
        }

        $geocodeService = new GeocodeService();
        $result = $geocodeService->searchByNominatimOnly($this->title);

        if (!is_array($result) || empty($result)) {
            $this->dispatch('show-toast', type: 'error', message: 'Keine gültigen Geodaten gefunden.');
            return;
        }

        $address = $result['address'] ?? [];

        $this->iso2 = strtoupper($address['country_code'] ?? '');
        $this->iso3 = strtoupper($address['ISO3166-2-lvl4'] ?? '');
        $this->lat = $result['lat'] ?? null;
        $this->lon = $result['lon'] ?? null;
        $this->bundesstaatLong = $address['state'] ?? null;
        $this->bundesstaatShort = $address['county'] ?? null;

        // Land automatisch setzen und Kontinent & Länder laden
        if ($this->iso2) {
            $country = WwdeCountry::where('country_code', $this->iso2)->first();

            if ($country) {
                $this->countryId = $country->id;
                $this->continentId = $country->continent_id;

                // Länder für gewählten Kontinent neu laden
                $this->countries = WwdeCountry::where('continent_id', $this->continentId)->get();
            } else {
                $this->dispatch('show-toast', type: 'error', message: 'Land nicht gefunden. Bitte manuell eingeben.');
            }
        }

        // Alias automatisch setzen, falls nicht bereits gesetzt
        if (empty($this->alias) && isset($this->title)) {
            $this->alias = str($this->title)->slug(); // Laravel Helper
        }

        $this->dispatch('show-toast', type: 'success', message: 'Daten erfolgreich abgerufen.');
    }



    public function render()
    {
        return view('livewire.backend.location-manager.location-create-component');
    }
}
