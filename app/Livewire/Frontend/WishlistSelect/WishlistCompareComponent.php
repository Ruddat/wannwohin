<?php

namespace App\Livewire\Frontend\WishlistSelect;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\WwdeLocation;
use App\Helpers\HeaderHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WishlistCompareComponent extends Component
{
    public $compareList = [];
    public $sortColumn = 'location_title'; // Standard-Sortierspalte
    public $sortDirection = 'asc'; // Standard-Richtung

    public function mount()
    {
        $this->compareList = session()->get('wishlist', []);

        if (!session()->has('headerData')) {
            $headerData = HeaderHelper::getHeaderContent();
            session(['headerData' => $headerData]);
        } else {
            $headerData = session('headerData');
        }

        $bgImgPath = $headerData['bgImgPath'];
        $mainImgPath = $headerData['mainImgPath'];
        $main_text = $headerData['title_text'];
    }

    public function removeFromCompare($locationId)
    {
        $wishlist = session()->get('wishlist', []);
        $wishlist = array_diff($wishlist, [$locationId]);
        session()->put('wishlist', $wishlist);
        $this->compareList = $wishlist;
        $this->dispatch('wishlistUpdated');
    }

    public function clearCompare()
    {
        session()->forget('wishlist');
        $this->compareList = [];
        $this->dispatch('wishlistUpdated');
    }

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $currentMonth = Carbon::now()->month;

        // Basisabfrage ohne price_trend-Sortierung
        $query = WwdeLocation::whereIn('wwde_locations.id', $this->compareList)
            ->leftJoin('wwde_climates', function ($join) use ($currentMonth) {
                $join->on('wwde_locations.id', '=', 'wwde_climates.location_id')
                     ->where('wwde_climates.month_id', '=', $currentMonth);
            })
            ->leftJoin('wwde_continents', 'wwde_locations.continent_id', '=', 'wwde_continents.id')
            ->leftJoin('wwde_countries', 'wwde_locations.country_id', '=', 'wwde_countries.id')
            ->select(
                'wwde_locations.id as location_id',
                'wwde_locations.title as location_title',
                'wwde_locations.alias as location_alias',
                'wwde_locations.iso2',
                'wwde_continents.alias as continent_alias',
                'wwde_countries.alias as country_alias',
                'wwde_locations.flight_hours',
                'wwde_locations.price_flight',
                DB::raw('MAX(wwde_climates.daily_temperature) as daily_temperature'),
                DB::raw('MAX(wwde_climates.night_temperature) as night_temperature'),
                DB::raw('MAX(wwde_climates.sunshine_per_day) as sunshine_per_day'),
                DB::raw('MAX(wwde_climates.rainy_days) as rainy_days'),
                DB::raw('MAX(wwde_climates.icon) as icon')
            )
            ->groupBy('wwde_locations.id');

        // Sortierung für alle Spalten außer price_trend in der DB
        if ($this->sortColumn !== 'price_trend') {
            $query->orderBy($this->sortColumn, $this->sortDirection);
        }

        $locations = $query->get();

        // Rundung der Werte
        foreach ($locations as $location) {
            $location->daily_temperature = $location->daily_temperature !== null ? round($location->daily_temperature, 1) : null;
            $location->sunshine_per_day = $location->sunshine_per_day !== null ? round($location->sunshine_per_day, 1) : null;
            $location->rainy_days = $location->rainy_days !== null ? round($location->rainy_days, 1) : null;
        }

        // Preistrend berechnen
        foreach ($locations as $location) {
            $cacheKey = "price_trend_{$location->iso2}";
            $priceTrend = Cache::remember($cacheKey, now()->addHours(24), function () use ($location) {
                return $this->calculatePriceTrend($location->iso2);
            });
            $location->price_trend = $priceTrend['category'] ?? 'N/A';
        }

        // Manuelle Sortierung für price_trend
        if ($this->sortColumn === 'price_trend') {
            $locations = $locations->sortBy(function ($location) {
                $order = ['niedrig' => 1, 'mittel' => 2, 'hoch' => 3, 'N/A' => 4];
                return $order[$location->price_trend] ?? 4;
            }, SORT_REGULAR, $this->sortDirection === 'desc');
        }

        return view('livewire.frontend.wishlist-select.wishlist-compare-component', compact('locations'));
    }

    protected function calculatePriceTrend(string $countryCode, string $referenceCountryCode = 'DE'): ?array
    {
        $cacheKey = "price_trend_{$countryCode}";
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($countryCode, $referenceCountryCode) {
            try {
                $countryIncome = $this->fetchIncomeData($countryCode);
                $referenceIncome = $this->fetchIncomeData($referenceCountryCode);

                if ($countryIncome && $referenceIncome) {
                    $trendFactor = $countryIncome / $referenceIncome;
                    $trendCategory = $trendFactor < 0.8 ? 'niedrig' : ($trendFactor <= 1.2 ? 'mittel' : 'hoch');
                    return [
                        'factor' => round($trendFactor, 2),
                        'category' => $trendCategory,
                    ];
                }
            } catch (\Exception $e) {
                Log::error("❌ Fehler beim Berechnen des Preistrends für {$countryCode}: {$e->getMessage()}");
            }
            return null;
        });
    }

    protected function fetchIncomeData(string $countryCode): ?float
    {
        $url = "https://api.worldbank.org/v2/country/{$countryCode}/indicator/NY.GDP.PCAP.CD?format=json";
        try {
            $response = Http::get($url);
            if ($response->successful()) {
                $data = $response->json();
                return $data[1][0]['value'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error("Fehler beim Abrufen der Einkommensdaten für {$countryCode}: {$e->getMessage()}");
        }
        return null;
    }
}
