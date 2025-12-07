<?php

namespace App\Services\LocationDetails;


use App\Models\WwdeLocation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PriceTrendService
{
    public function get(WwdeLocation $location)
    {
        if (!$location->iso2) {
            return null;
        }

        $country = $location->iso2;
        $reference = 'DE';

        $cacheKey = "price_trend_{$country}_{$reference}";

        return Cache::remember($cacheKey, 60 * 60 * 24, function () use ($country, $reference) {
            $countryIncome = $this->income($country);
            $referenceIncome = $this->income($reference);

            if (!$countryIncome || !$referenceIncome) {
                return null;
            }

            $factor = $countryIncome / $referenceIncome;

            return [
                'factor' => round($factor, 2),
                'category' => $factor < 0.8 ? 'niedrig' : ($factor <= 1.2 ? 'mittel' : 'hoch'),
            ];
        });
    }

    private function income(string $code): ?float
    {
        $key = "income_data_$code";

        return Cache::remember($key, 60 * 60 * 24 * 7, function () use ($code) {
            try {
                $response = Http::get("https://api.worldbank.org/v2/country/{$code}/indicator/NY.GDP.PCAP.CD?format=json");

                if (!$response->successful()) {
                    return null;
                }

                $value = $response->json()[1][0]['value'] ?? null;

                return $value ? floatval($value) : null;

            } catch (\Exception $e) {
                Log::error("Income fetch failed for {$code}: ".$e->getMessage());
                return null;
            }
        });
    }
}
