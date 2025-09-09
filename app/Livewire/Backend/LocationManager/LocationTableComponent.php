<?php

namespace App\Livewire\Backend\LocationManager;

use Livewire\Component;
use App\Models\WwdeCountry;
use App\Models\WwdeLocation;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LocationTableComponent extends Component
{
    use WithPagination, WithoutUrlPagination, WithFileUploads;

    public $search = '';
    public $perPage = 10;
    public $filterCountry = '';
    public $filterStatus = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $filterDeleted = '';
    public $excelFile; // Für den Datei-Upload
    public $skipImages = false;
    public $exportFailed = false;
    public $isLoading = false;


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
            'ID', 'Old Id', 'Continent ID', 'Country ID', 'ISO2', 'ISO3', 'Title', 'Alias', 'IATA Code', 'Flight Hours', 'Stop Over',
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
                $location->old_id,
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


    public function importLocations()
    {
        try {
            $this->validate([
                'excelFile' => 'required|file|mimes:xlsx,xls|max:20480', // max 20MB
            ]);

            $file = $this->excelFile->getRealPath();
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $headers = array_shift($rows);
            $failedRows = [];
            $importedCount = 0;

            $columnMap = [
                'id' => 0, 'continent_id' => 1, 'country_id' => 2, 'iso2' => 3, 'iso3' => 4,
                'title' => 5, 'alias' => 6, 'iata_code' => 7, 'flight_hours' => 8, 'stop_over' => 9,
                'dist_from_FRA' => 10, 'dist_type' => 11, 'lat' => 12, 'lon' => 13, 'station_id' => 14,
                'bundesstaat_long' => 15, 'bundesstaat_short' => 16, 'no_city_but' => 17, 'population' => 18,
                'list_beach' => 19, 'list_citytravel' => 20, 'list_sports' => 21, 'list_island' => 22,
                'list_culture' => 23, 'list_nature' => 24, 'list_watersport' => 25, 'list_wintersport' => 26,
                'list_mountainsport' => 27, 'list_biking' => 28, 'list_fishing' => 29,
                'list_amusement_park' => 30, 'list_water_park' => 31, 'list_animal_park' => 32,
                'best_traveltime' => 33, 'pic1_text' => 34, 'pic2_text' => 35, 'pic3_text' => 36,
                'text_headline' => 37, 'text_short' => 38, 'text_location_climate' => 39,
                'text_what_to_do' => 40, 'text_best_traveltime' => 41, 'text_sports' => 42,
                'text_amusement_parks' => 43, 'climate_details_id' => 44, 'climate_lnam' => 45,
                'climate_details_lnam' => 46, 'price_flight' => 47, 'range_flight' => 48,
                'price_hotel' => 49, 'range_hotel' => 50, 'price_rental' => 51, 'range_rental' => 52,
                'price_travel' => 53, 'range_travel' => 54, 'finished' => 55,
                'best_traveltime_json' => 56, 'panorama_text_and_style' => 57, 'time_zone' => 58,
                'lat_new' => 59, 'lon_new' => 60, 'text_pic1' => 61, 'text_pic2' => 62,
                'text_pic3' => 63, 'status' => 64, 'created_at' => 65, 'updated_at' => 66
            ];

            foreach ($rows as $index => $row) {
                try {
                    $locationData = [];
                    foreach ($columnMap as $field => $columnIndex) {
                        $value = $row[$columnIndex] ?? null;

                        switch ($field) {
                            case 'id':
                            case 'continent_id':
                            case 'country_id':
                            case 'climate_details_id':
                            case 'stop_over':
                            case 'dist_from_FRA':
                            case 'population':
                            case 'price_flight':
                            case 'range_flight':
                            case 'price_hotel':
                            case 'range_hotel':
                            case 'price_rental':
                            case 'range_rental':
                            case 'price_travel':
                            case 'range_travel':
                                $locationData[$field] = $value ? (int)$value : null;
                                break;
                            case 'flight_hours':
                                $locationData[$field] = $value ? (float)$value : null;
                                break;
                            case 'list_beach':
                            case 'list_citytravel':
                            case 'list_sports':
                            case 'list_island':
                            case 'list_culture':
                            case 'list_nature':
                            case 'list_watersport':
                            case 'list_wintersport':
                            case 'list_mountainsport':
                            case 'list_biking':
                            case 'list_fishing':
                            case 'list_amusement_park':
                            case 'list_water_park':
                            case 'list_animal_park':
                            case 'finished':
                                $locationData[$field] = $value ? (bool)$value : false;
                                break;
                            case 'status':
                                $locationData[$field] = in_array($value, ['active', 'pending', 'inactive']) ? $value : 'active';
                                break;
                            case 'created_at':
                            case 'updated_at':
                                $locationData[$field] = $value ? date('Y-m-d H:i:s', strtotime($value)) : null;
                                break;
                            default:
                                $locationData[$field] = $value;
                        }
                    }

                    $validator = Validator::make($locationData, [
                        'title' => 'required|string|max:50',
                        'status' => 'required|in:active,pending,inactive',
                    ]);

                    if ($validator->fails()) {
                        throw new \Exception($validator->errors()->first());
                    }

                    WwdeLocation::updateOrCreate(
                        ['id' => $locationData['id']],
                        $locationData
                    );

                    $importedCount++;

                } catch (\Exception $e) {
                    $failedRows[] = array_merge($row, ['error' => $e->getMessage()]);
                }
            }

            if ($this->exportFailed && !empty($failedRows)) {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $headers[] = 'Error';
                $sheet->fromArray($headers, NULL, 'A1');
                $sheet->fromArray($failedRows, NULL, 'A2');

                return Response::streamDownload(function () use ($spreadsheet) {
                    $writer = new Xlsx($spreadsheet);
                    $writer->save('php://output');
                }, 'failed_locations_import.xlsx');
            }

            $this->dispatch('showSuccessMessage', "Erfolgreich $importedCount Standorte importiert. " .
                (count($failedRows) > 0 ? count($failedRows) . " Zeilen fehlerhaft." : ""));

            $this->reset(['excelFile', 'skipImages', 'exportFailed']);
            $this->dispatch('refreshLocations');

        } catch (\Exception $e) {
            $this->dispatch('showErrorMessage', 'Import fehlgeschlagen: ' . $e->getMessage());
        }
    }






    public function confirmDelete($locationId)
    {
        Log::info('confirmDelete called with ID:', ['locationId' => $locationId]);
        $this->dispatch('triggerDeleteConfirmation', $locationId);
    }

    public function deleteLocation($locationId)
    {
        Log::info('deleteLocation called with ID:', ['locationId' => $locationId]);
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
        } else {
            $this->dispatch('showErrorMessage', 'Standort nicht gefunden.');
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
