<?php

namespace App\Livewire\Frontend\QuickSearch;

use Livewire\Component;
use App\Models\WwdeLocation;
use Livewire\WithPagination;
use App\Models\HeaderContent;
use Illuminate\Support\Facades\Storage;
use App\Repositories\LocationRepository;

class SearchResultsComponent extends Component
{
    use WithPagination;

    public $continent;
    public $price;
    public $urlaub;
    public $sonnenstunden;
    public $wassertemperatur;
    public $spezielle;
    public $nurInBesterReisezeit = false;

    public $sortBy = 'title';
    public $sortDirection = 'asc';

    public $headerContent;
    public $bgImgPath;
    public $mainImgPath;

    public function mount(LocationRepository $repository)
    {
        // Header Content und Bilder laden
        $headerData = $repository->getHeaderContent();
        session()->put('headerData', [
            'headerContent' => $headerData['headerContent'] ?? null,
            'bgImgPath' => $headerData['bgImgPath'] ?? null,
            'mainImgPath' => $headerData['mainImgPath'] ?? null,
        ]);

         // HeaderContent abrufen
         $headerContent = HeaderContent::inRandomOrder()->first();

         // Bildpfade validieren
         $bgImgPath = $headerContent->bg_img ?
             (Storage::exists($headerContent->bg_img)
                 ? Storage::url($headerContent->bg_img)
                 : (file_exists(public_path($headerContent->bg_img))
                     ? asset($headerContent->bg_img)
                     : null))
             : null;

         $mainImgPath = $headerContent->main_img ?
             (Storage::exists($headerContent->main_img)
                 ? Storage::url($headerContent->main_img)
                 : (file_exists(public_path($headerContent->main_img))
                     ? asset($headerContent->main_img)
                     : null))
             : null;

             session([
                'headerData' => [
                    'bgImgPath' => $bgImgPath,
                    'mainImgPath' => $mainImgPath,
                    'title' => $headerContent->title,
                    'title_text' => $headerContent->main_text,
                    'main_text' => $headerContent->content,
                ]
            ]);



        // Suchparameter aus der URL laden
        $this->continent = request('continent');
        $this->price = request('price');
        $this->urlaub = request('urlaub');
        $this->sonnenstunden = request('sonnenstunden');
        $this->wassertemperatur = request('wassertemperatur');
        $this->spezielle = request('spezielle');
        $this->nurInBesterReisezeit = request('nurInBesterReisezeit', false);
    }

    public function updatedSortBy()
    {
        $this->resetPage(); // Pagination zurücksetzen, wenn die Sortierung geändert wird
    }

    public function render()
    {
        $query = WwdeLocation::query()
            ->select('wwde_locations.*') // Alle Spalten von Locations
            ->with('climates') // Alle Klimadaten mitladen
            ->active()
            ->filterByContinent($this->continent)
            ->filterByPrice($this->price)
            ->filterBySunshine($this->sonnenstunden)
            ->filterByWaterTemperature($this->wassertemperatur)
            ->filterBySpecials($this->spezielle);

        // **Filter nach Reisezeit**
        if (!empty($this->urlaub) && is_numeric($this->urlaub)) {
            $monthNumber = (int) $this->urlaub;

            if ($this->nurInBesterReisezeit) {
                $query->whereRaw('JSON_CONTAINS(best_traveltime_json, ?)', [json_encode($monthNumber)]);
            }
        }

        // **Sortieren und paginieren**
        $locations = $query->orderBy("wwde_locations.{$this->sortBy}", $this->sortDirection)
            ->paginate(10);

        return view('livewire.frontend.quick-search.search-results-component', [
            'locations' => $locations,
            'selectedMonth' => $this->urlaub, // Monat ans Blade übergeben
        ]);
    }



}
