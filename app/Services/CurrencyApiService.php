<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Models\ModCurrencyExchangeRate;

class CurrencyApiService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('EXCHANGE_RATE_API_KEY', '5b591ae59baf7f487e9659f0');
    }

    public function updateAllExchangeRates()
    {
        try {
            // API für USD als Basiswährung abrufen
            $url = "https://v6.exchangerate-api.com/v6/{$this->apiKey}/latest/USD";
            $response = $this->client->get($url);
            $data = json_decode($response->getBody(), true);

            if (!isset($data['conversion_rates'])) {
                Log::error("API-Fehler: Keine Wechselkurse gefunden.");
                return false;
            }

            $baseCurrency = $data['base_code'];
            $conversionRates = $data['conversion_rates'];

            // Alle verfügbaren Währungen speichern
            foreach ($conversionRates as $targetCurrency => $rate) {
                MOdCurrencyExchangeRate::updateOrCreate(
                    ['base_currency' => $baseCurrency, 'target_currency' => $targetCurrency],
                    ['exchange_rate' => $rate, 'last_updated' => Carbon::now()]
                );
            }

            // Auch für andere Basiswährungen abrufen (EUR, GBP, JPY, etc.)
            $currencies = array_keys($conversionRates);
            foreach ($currencies as $newBase) {
                if ($newBase !== $baseCurrency) {
                    $this->fetchAndStoreRates($newBase);
                }
            }

            Log::info("Alle Wechselkurse erfolgreich aktualisiert.");
            return true;
        } catch (\Exception $e) {
            Log::error("Fehler beim Abrufen der Wechselkurse: " . $e->getMessage());
            return false;
        }
    }

    private function fetchAndStoreRates($base)
    {
        try {
            $url = "https://v6.exchangerate-api.com/v6/{$this->apiKey}/latest/{$base}";
            $response = $this->client->get($url);
            $data = json_decode($response->getBody(), true);

            if (!isset($data['conversion_rates'])) {
                return false;
            }

            foreach ($data['conversion_rates'] as $target => $rate) {
                ModCurrencyExchangeRate::updateOrCreate(
                    ['base_currency' => $base, 'target_currency' => $target],
                    ['exchange_rate' => $rate, 'last_updated' => Carbon::now()]
                );
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Fehler bei {$base}: " . $e->getMessage());
            return false;
        }
    }
}
