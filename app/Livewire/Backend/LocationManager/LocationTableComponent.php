<?php

namespace App\Livewire\Backend\LocationManager;

use Livewire\Component;
use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LocationTableComponent extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $search = '';
    public $perPage = 10;
    public $filterCountry = '';
    public $filterStatus = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $filterDeleted = '';

    protected $listeners = [
        'refreshLocations' => '$refresh',
        'deleteConfirmed' => 'deleteLocation',
        'forceDeleteConfirmed' => 'forceDeleteLocation',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function gotoPage($page)
    {
        $this->setPage($page);
    }


    public function toggleStatus($locationId)
    {
        $location = WwdeLocation::find($locationId);
        if ($location) {
            $location->status = match ($location->status) {
                'active' => 'pending',
                'pending' => 'inactive',
                default => 'active',
            };
            $location->save();
            $this->dispatch('refreshLocations');
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterCountry', 'filterStatus', 'perPage', 'sortField', 'sortDirection']);
        $this->resetPage();
    }

    public function openEditModal($locationId)
    {
        $this->isLoading = true; // Ladeanzeige beenden
        $this->dispatch('openEditModal', $locationId); // Ereignis an LocationManagerComponent senden
    }

    public function exportLocations()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header setzen
        $headers = [
            'ID', 'Continent ID', 'Country ID', 'ISO2', 'ISO3', 'Title', 'Alias', 'IATA Code', 'Flight Hours', 'Stop Over',
            'Distance from FRA', 'Distance Type', 'Latitude', 'Longitude', 'Station ID', 'Bundesstaat Long', 'Bundesstaat Short',
            'No City But', 'Population', 'List Beach', 'List Citytravel', 'List Sports', 'List Island', 'List Culture',
            'List Nature', 'List Watersport', 'List Wintersport', 'List Mountainsport', 'List Biking', 'List Fishing',
            'List Amusement Park', 'List Water Park', 'List Animal Park', 'Best Travel Time', 'Pic1 Text', 'Pic2 Text',
            'Pic3 Text', 'Text Headline', 'Text Short', 'Text Location Climate', 'Text What To Do', 'Text Best Travel Time',
            'Text Sports', 'Text Amusement Parks', 'Climate Details ID', 'Climate LNAM', 'Climate Details LNAM',
            'Price Flight', 'Range Flight', 'Price Hotel', 'Range Hotel', 'Price Rental', 'Range Rental', 'Price Travel',
            'Range Travel', 'Finished', 'Best Travel Time JSON', 'Panorama Text and Style', 'Time Zone', 'Lat New', 'Lon New',
            'Text Pic1', 'Text Pic2', 'Text Pic3', 'Status', 'Created At', 'Updated At'
        ];
        $sheet->fromArray([$headers], NULL, 'A1');

        // Daten abrufen und einfügen
        $locations = WwdeLocation::with('country')->get();
        $row = 2; // Startreihe nach den Headers

        foreach ($locations as $location) {
            $sheet->fromArray([
                $location->id,
                $location->continent_id,
                $location->country_id,
                $location->iso2,
                $location->iso3,
                $location->title,
                $location->alias,
                $location->iata_code,
                $location->flight_hours,
                $location->stop_over,
                $location->dist_from_FRA,
                $location->dist_type,
                $location->lat,
                $location->lon,
                $location->station_id,
                $location->bundesstaat_long,
                $location->bundesstaat_short,
                $location->no_city_but,
                $location->population,
                $location->list_beach,
                $location->list_citytravel,
                $location->list_sports,
                $location->list_island,
                $location->list_culture,
                $location->list_nature,
                $location->list_watersport,
                $location->list_wintersport,
                $location->list_mountainsport,
                $location->list_biking,
                $location->list_fishing,
                $location->list_amusement_park,
                $location->list_water_park,
                $location->list_animal_park,
                $location->best_traveltime,
                $location->pic1_text,
                $location->pic2_text,
                $location->pic3_text,
                $location->text_headline,
                $location->text_short,
                $location->text_location_climate,
                $location->text_what_to_do,
                $location->text_best_traveltime,
                $location->text_sports,
                $location->text_amusement_parks,
                $location->climate_details_id,
                $location->climate_lnam,
                $location->climate_details_lnam,
                $location->price_flight,
                $location->range_flight,
                $location->price_hotel,
                $location->range_hotel,
                $location->price_rental,
                $location->range_rental,
                $location->price_travel,
                $location->range_travel,
                $location->finished,
                $location->best_traveltime_json,
                $location->panorama_text_and_style,
                $location->time_zone,
                $location->lat_new,
                $location->lon_new,
                $location->text_pic1,
                $location->text_pic2,
                $location->text_pic3,
                $location->status,
                $location->created_at,
                $location->updated_at,
            ], NULL, "A$row");

            $row++;
        }

        // Streamed Response für den Datei-Download
        return Response::streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'locations_export.xlsx');
    }

    public function confirmDelete($locationId)
    {
        \Log::info('confirmDelete called with ID:', ['locationId' => $locationId]);
        $this->dispatch('triggerDeleteConfirmation', $locationId);
    }

    public function deleteLocation($locationId)
    {
        \Log::info('deleteLocation called with ID:', ['locationId' => $locationId]);
        $location = WwdeLocation::find($locationId);
        if ($location instanceof WwdeLocation) {
            $location->delete();
            $this->dispatch('refreshLocations');
            $this->dispatch('showSuccessMessage', 'Die Location wurde erfolgreich gelöscht.');
        }
    }

    public function restoreLocation($locationId)
    {
        $location = WwdeLocation::withTrashed()->find($locationId);
        if ($location) {
            $location->restore();
            $this->dispatch('refreshLocations');
            $this->dispatch('showSuccessMessage', 'Die Location wurde erfolgreich wiederhergestellt.');
        }
    }

    public function forceDeleteLocation($locationId)
    {
        $location = WwdeLocation::withTrashed()->find($locationId);
        if ($location) {
            $location->forceDelete();
            $this->dispatch('refreshLocations');
            $this->dispatch('showSuccessMessage', 'Die Location wurde dauerhaft gelöscht.');
        }
    }

    public function sortBy($field)
    {
        // Liste der erlaubten Felder zur Sicherheit
        $sortableFields = ['id', 'title', 'iata_code', 'country', 'status'];
        if (!in_array($field, $sortableFields)) {
            return; // Ungültiges Feld ignorieren
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage(); // Zurück zur ersten Seite nach Sortierung
    }

    public function render()
    {
        $locations = WwdeLocation::query()
            ->with('country')
            ->when($this->search, fn($query) => $query->where('title', 'like', "%{$this->search}%")->orWhere('iata_code', 'like', "%{$this->search}%"))
            ->when($this->filterCountry, fn($query) => $query->where('country_id', $this->filterCountry))
            ->when($this->filterStatus, fn($query) => $query->where('status', $this->filterStatus))
            ->when($this->filterDeleted === 'only_deleted', fn($query) => $query->onlyTrashed())
            ->when($this->filterDeleted === 'with_deleted', fn($query) => $query->withTrashed())
            ->when($this->sortField === 'country', function ($query) {
                $query->join('wwde_countries as c', 'c.id', '=', 'wwde_locations.country_id')
                      ->orderBy('c.title', $this->sortDirection)
                      ->select('wwde_locations.*'); // Vermeide Spaltenkonflikte
            }, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate($this->perPage);

        $countries = WwdeCountry::all();

        return view('livewire.backend.location-manager.location-table-component', [
            'locations' => $locations,
            'countries' => $countries,
        ])->layout('raadmin.layout.master');
    }
}
