<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GeocodeService
{
    protected $client;
    protected $userAgent;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 2.0, // Timeout auf 2 Sekunden erhöhen
        ]);

        // User-Agent setzen, um Blockierungen zu vermeiden
        $this->userAgent = 'MyAppName/1.0 (+https://mywebsite.com; contact@myemail.com)';
    }

    /**
     * Suche nach einer Stadt und gib die notwendigen Daten zurück.
     */
    public function searchByAddress($cityName)
    {
        // Prüfe, ob die Stadt bereits gecacht ist
        $cacheKey = 'geocode_' . md5($cityName);
        if (Cache::has($cacheKey)) {
            Log::info("Using cached geocode data for: {$cityName}");
            return Cache::get($cacheKey);
        }

        // Schritt 1: Restcountries API zuerst nutzen
        $isoData = $this->getCountryByCity($cityName);
        if (!empty($isoData['iso2']) && !empty($isoData['iso3'])) {
            // Falls Restcountries das Land kennt, suchen wir nur noch Koordinaten
            $geoData = $this->searchCoordinatesByCity($cityName);

            $result = [
                'address' => [
                    'country' => $isoData['country_name'],
                    'country_code' => strtolower($isoData['iso2']),
                    'ISO3166-2-lvl4' => $isoData['iso3']
                ],
                'lat' => $geoData['lat'] ?? null,
                'lon' => $geoData['lon'] ?? null
            ];

            // Speichern im Cache
            Cache::put($cacheKey, $result, now()->addDays(7));

            return $result;
        }

        // Schritt 2: OpenStreetMap nur als Fallback
        return $this->searchByNominatim($cityName, $cacheKey);
    }

    /**
     * Hole ISO-Codes und den offiziellen Ländernamen über Restcountries API.
     */
    private function getCountryByCity(string $cityName): array
    {
        $response = Http::get('https://restcountries.com/v3.1/capital/' . urlencode($cityName));
        if ($response->successful() && $response->json()) {
            $data = $response->json()[0] ?? [];

            return [
                'iso2' => $data['cca2'] ?? null,
                'iso3' => $data['cca3'] ?? null,
                'country_name' => $data['name']['common'] ?? null,
            ];
        }
        return ['iso2' => null, 'iso3' => null, 'country_name' => null];
    }

    /**
     * Suche nach den Koordinaten einer Stadt.
     */
    private function searchCoordinatesByCity(string $cityName): array
    {
        // Überprüfe, ob die Stadt bereits gecacht ist
        $cacheKey = 'coordinates_' . md5($cityName);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // OSM API als Fallback nutzen
        $geoData = $this->searchByNominatim($cityName, $cacheKey);

        return [
            'lat' => $geoData['lat'] ?? null,
            'lon' => $geoData['lon'] ?? null,
        ];
    }

    /**
     * Suche nach einer Stadt über OpenStreetMap (OSM/Nominatim) als Fallback.
     */
    private function searchByNominatim(string $query, string $cacheKey)
    {
        $url = "https://nominatim.openstreetmap.org/search";
        $params = [
            'query' => [
                'q' => $query,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 1, // Nur das erste Ergebnis
            ],
            'headers' => $this->getDefaultHeaders(),
        ];

        $result = $this->sendRequestWithRetries($url, $params);

        // Falls erfolgreich, Daten cachen
        if ($result) {
            Cache::put($cacheKey, $result, now()->addDays(7));
        }

        return $result;
    }

    public function searchByParkName($query)
    {
        $url = "https://nominatim.openstreetmap.org/search";
        $params = [
            'query' => [
                'q' => $query, // Nur der Parkname
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 1, // Nur das erste Ergebnis
            ],
            'headers' => $this->getDefaultHeaders(),
        ];

        // Replace sendRequest with sendRequestWithRetries
        return $this->sendRequestWithRetries($url, $params);
    }


    /**
     * Sende eine HTTP-Anfrage mit automatischer Wiederholung bei Fehlern.
     */
    private function sendRequestWithRetries($url, $params, $maxRetries = 3, $delay = 2)
    {
        for ($i = 0; $i < $maxRetries; $i++) {
            try {
                $response = $this->client->get($url, $params);

                if ($response->getStatusCode() === 200) {
                    return json_decode($response->getBody(), true);
                }

                Log::warning("Unexpected API response ({$response->getStatusCode()}) for query: {$params['query']['q']}");
            } catch (RequestException $e) {
                Log::error("Geocoding error: " . $e->getMessage());
            }

            // Wartezeit vor erneutem Versuch
            sleep($delay);
        }

        return null;
    }


/**
 * Suche nach Adressdaten basierend auf Koordinaten (Breitengrad und Längengrad).
 */
public function searchByCoordinates(float $lat, float $lon): array
{
    // Cache-Schlüssel für die Koordinaten
    $cacheKey = 'geocode_coords_' . md5("{$lat}_{$lon}");

    // Überprüfen, ob die Daten bereits im Cache sind
    if (Cache::has($cacheKey)) {
        Log::info("Using cached geocode data for coordinates: {$lat}, {$lon}");
        return Cache::get($cacheKey);
    }

    // URL für die Nominatim-API (Reverse Geocoding)
    $url = "https://nominatim.openstreetmap.org/reverse";
    $params = [
        'query' => [
            'lat' => $lat,
            'lon' => $lon,
            'format' => 'json',
            'addressdetails' => 1,
        ],
        'headers' => $this->getDefaultHeaders(),
    ];

    // Anfrage senden
    $result = $this->sendRequestWithRetries($url, $params);

    // Ergebnis validieren und cachen
    if ($result && isset($result['address'])) {
        $addressData = [
            'address' => $result['address'],
            'lat' => $lat,
            'lon' => $lon,
        ];

        // Daten im Cache speichern
        Cache::put($cacheKey, $addressData, now()->addDays(7));

        return $addressData;
    }

    // Fallback, falls keine Daten gefunden wurden
    return [
        'address' => [
            'country' => 'Unknown',
            'country_code' => 'unknown',
            'ISO3166-2-lvl4' => 'unknown',
        ],
        'lat' => $lat,
        'lon' => $lon,
    ];
}

/**
 * Suche nach einer Stadt über Nominatim (OpenStreetMap) und gib die Koordinaten und ISO-Codes zurück.
 */
public function searchByNominatimOnly(string $cityName): array
{
    $url = "https://nominatim.openstreetmap.org/search";
    $params = [
        'query' => [
            'q' => $cityName,
            'format' => 'json',
            'addressdetails' => 1,
            'limit' => 1, // Nur das erste Ergebnis
        ],
        'headers' => $this->getDefaultHeaders(),
    ];

    $result = $this->sendRequestWithRetries($url, $params);

    if ($result && !empty($result[0])) {
        $data = $result[0];

        return [
            'address' => [
                'country' => $data['address']['country'] ?? 'Unknown',
                'country_code' => strtolower($data['address']['country_code'] ?? 'unknown'),
                'ISO3166-2-lvl4' => strtoupper($data['address']['ISO3166-2-lvl4'] ?? 'unknown'),
                'state' => $data['address']['state'] ?? null, // Bundesstaat
                'county' => $data['address']['county'] ?? null, // Landkreis oder Region
                'region' => $data['address']['region'] ?? null, // Region falls vorhanden
                'city' => $data['address']['city'] ?? $data['address']['town'] ?? $data['address']['village'] ?? null, // Stadt erkennen
            ],
            'lat' => $data['lat'] ?? null,
            'lon' => $data['lon'] ?? null,
        ];
    }

    return [
        'address' => [
            'country' => 'Unknown',
            'country_code' => 'unknown',
            'ISO3166-2-lvl4' => 'unknown',
            'state' => null,
            'county' => null,
            'region' => null,
            'city' => null,
        ],
        'lat' => null,
        'lon' => null,
    ];
}


    /**
     * Standard-Header für HTTP-Anfragen.
     */
    private function getDefaultHeaders()
    {
        return [
            'User-Agent' => $this->userAgent,
            'Accept' => 'application/json',
        ];
    }
}
