<?php

namespace App\Services\LocationDetails;

use App\Models\WwdeLocation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PriceTrendService
{
    public function get(WwdeLocation $location): ?array
    {
        $countryCode = $location->country->iso2 ?? $location->iso2;

        if (!$countryCode) {
            return null;
        }

        $reference = 'DE';
        $cacheKey = "price_trend_{$countryCode}_{$reference}";

        return Cache::remember($cacheKey, 60 * 60 * 24, function () use ($countryCode, $reference) {

            $countryIncome   = $this->income($countryCode);
            $referenceIncome = $this->income($reference);

            if (!$countryIncome || !$referenceIncome || $referenceIncome == 0) {
                return null;
            }

            $factor = $countryIncome / $referenceIncome;

            return [
                'factor'   => round($factor, 2),
                'category' => $this->category($factor),
            ];
        });
    }

    private function income(string $code): ?float
    {
        $cacheKey = "income_data_{$code}";

        return Cache::remember($cacheKey, 60 * 60 * 24 * 7, function () use ($code) {

            // Lock verhindert parallele API-Calls
            return Cache::lock("income_lock_{$code}", 10)->block(5, function () use ($code) {

                try {
                    $response = Http::timeout(8)
                        ->retry(2, 300)
                        ->get("https://api.worldbank.org/v2/country/{$code}/indicator/NY.GDP.PCAP.CD?format=json");

                    if (!$response->successful()) {
                        return null;
                    }

                    $data = $response->json();

                    if (!isset($data[1]) || !is_array($data[1])) {
                        return null;
                    }

                    // ersten gültigen Wert nehmen
                    foreach ($data[1] as $row) {
                        if (!empty($row['value'])) {
                            return (float) $row['value'];
                        }
                    }

                    return null;

                } catch (\Throwable $e) {
                    Log::warning("Income fetch failed for {$code}: " . $e->getMessage());
                    return null;
                }
            });
        });
    }

    private function category(float $factor): string
    {
        return match (true) {
            $factor < 0.8  => 'niedrig',
            $factor <= 1.2 => 'mittel',
            default        => 'hoch',
        };
    }
}
