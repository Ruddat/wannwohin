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

    public function mount()
    {
        // Wishlist-IDs aus der Session holen
        $this->compareList = session()->get('wishlist', []);



        if (!session()->has('headerData')) {
            $headerData = HeaderHelper::getHeaderContent();
            session(['headerData' => $headerData]);
        } else {
            $headerData = session('headerData');
        }


//dd($headerData);

        // Zugriff auf Variablen
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

        // Event zum Aktualisieren der Wishlist-Komponente auslösen
        $this->dispatch('wishlistUpdated');
    }

    public function clearCompare()
    {
        session()->forget('wishlist');
        $this->compareList = [];

        // Event zum Aktualisieren der Wishlist-Komponente auslösen
        $this->dispatch('wishlistUpdated');
    }

    public function render()
    {
        $currentMonth = Carbon::now()->month; // Aktueller Monat (1-12)

        $locations = WwdeLocation::whereIn('wwde_locations.id', $this->compareList)
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
            'wwde_climates.daily_temperature',
            'wwde_climates.night_temperature',
            'wwde_climates.sunshine_per_day',
            'wwde_climates.rainy_days',
            'wwde_climates.icon'
        )
        ->get();

    // 🌍 Preistrend für jedes Land aus Cache holen oder berechnen
    foreach ($locations as $location) {
        $cacheKey = "price_trend_{$location->iso2}";
        $priceTrend = Cache::remember($cacheKey, now()->addHours(24), function () use ($location) {
            return $this->calculatePriceTrend($location->iso2);
        });

        $location->price_trend = $priceTrend['category'] ?? 'N/A';
    }

        return view('livewire.frontend.wishlist-select.wishlist-compare-component', compact('locations'));
    }

/**
 * Berechnet den Preistrend für ein Land im Vergleich zu einem Referenzland.
 *
 * @param string $countryCode Der ISO2-Code des Landes.
 * @param string $referenceCountryCode Der ISO2-Code des Referenzlandes (Standard: Deutschland).
 * @return array|null Gibt ein Array mit dem Trendfaktor und der Kategorie zurück oder null bei einem Fehler.
 */
protected function calculatePriceTrend(string $countryCode, string $referenceCountryCode = 'DE'): ?array
{
        // Cache-Key für den Preisindex
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

    /**
     * Holt das durchschnittliche Einkommen eines Landes aus der World Bank API.
     */
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
