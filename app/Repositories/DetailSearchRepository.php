<?php

namespace App\Repositories;

use App\Models\WwdeCountry;
use Illuminate\Support\Str;
use App\Models\WwdeLocation;
use App\Models\WwdeContinent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DetailSearchRepository
{
    /**
     * Get all filter options for detail search
     */
    public function getFilterOptions(): array
    {
        return [
            'continents' => $this->getContinents(),
            'countries' => $this->getActiveCountries(),
            'currencies' => $this->getCurrencies(),
         //   'languages' => $this->getLanguages(),
         //   'flight_durations' => $this->getFlightDurations(),
        //    'climate_zones' => $this->getClimateZones(),
            'price_tendencies' => $this->getPriceTendencies(),
       //     'activities' => $this->getActivities(),
       //     'months' => $this->getMonths(),

    'price_flight' => [
    300 => 'bis 300 €',
    500 => 'bis 500 €',
    800 => 'bis 800 €',
    1200 => 'bis 1.200 €',
],

'price_hotel' => [
    50 => 'bis 50 € / Nacht',
    100 => 'bis 100 € / Nacht',
    150 => 'bis 150 € / Nacht',
],

'price_mietwagen' => [
    30 => 'bis 30 € / Tag',
    50 => 'bis 50 € / Tag',
    80 => 'bis 80 € / Tag',
],

'price_pauschalreise' => [
    800 => 'bis 800 €',
    1200 => 'bis 1.200 €',
    2000 => 'bis 2.000 €',
],


    ];
    }

    // [Hier bleiben alle bestehenden Methoden...]


    /**
     * Build search query based on filters
     */
    protected function buildSearchQuery(array $filters)
    {
        $query = WwdeLocation::query()
            ->with(['country.continent'])
            ->where('status', 'active');

        // Kontinente Filter
        if (!empty($filters['continents'])) {
            $continentIds = array_keys(array_filter($filters['continents']));

            // Hole Länder IDs für diese Kontinente
            $countryIds = WwdeCountry::whereIn('continent_id', $continentIds)
                ->active()
                ->pluck('id')
                ->toArray();

            $query->whereIn('country_id', $countryIds);
        }

        // Land Filter
        if (!empty($filters['country'])) {
            $query->where('country_id', $filters['country']);
        }

        // Klimazone Filter
        if (!empty($filters['climate_zone'])) {
            $query->where('climate_lnam', 'LIKE', '%' . $filters['climate_zone'] . '%');
        }

        // Monat Filter (basierend auf best_traveltime_json)
        if (!empty($filters['month'])) {
            $query->whereJsonContains('best_traveltime_json', (int)$filters['month']);
        }

        // Preis-Tendenz Filter
        if (!empty($filters['price_tendency'])) {
            $query->whereHas('country', function($q) use ($filters) {
                $q->where('price_tendency', $filters['price_tendency']);
            });
        }

        // Währung Filter
        if (!empty($filters['currency'])) {
            $query->whereHas('country', function($q) use ($filters) {
                $q->where('currency_code', $filters['currency']);
            });
        }

        // Sprache Filter
        if (!empty($filters['language'])) {
            $query->whereHas('country', function($q) use ($filters) {
                $q->where('official_language', 'LIKE', '%' . $filters['language'] . '%');
            });
        }

        // Flugdauer Filter
        if (!empty($filters['flight_duration'])) {
            $query->where(function($q) use ($filters) {
                switch ($filters['flight_duration']) {
                    case 'short':
                        $q->where('flight_hours', '<=', 4);
                        break;
                    case 'medium':
                        $q->whereBetween('flight_hours', [4, 8]);
                        break;
                    case 'long':
                        $q->where('flight_hours', '>', 8);
                        break;
                }
            });
        }

// =========================
// 💰 Preis-Filter
// =========================

// ✈ Flugpreis
if (!empty($filters['price_flight'])) {
    $query->where('price_flight', '<=', (int) $filters['price_flight']);
}

// 🏨 Hotelpreis
if (!empty($filters['price_hotel'])) {
    $query->where('price_hotel', '<=', (int) $filters['price_hotel']);
}

// 🚗 Mietwagen
if (!empty($filters['price_mietwagen'])) {
    $query->where('price_mietwagen', '<=', (int) $filters['price_mietwagen']);
}

// 🧳 Pauschalreise
if (!empty($filters['price_pauschalreise'])) {
    $query->where('price_pauschalreise', '<=', (int) $filters['price_pauschalreise']);
}

// Monat
if (!empty($filters['month'])) {
    $query->whereJsonContains('best_traveltime_json', (int)$filters['month']);
}

// Preis pro Person (Range-ID)
if (!empty($filters['range_flight'])) {
    $query->where('price_flight', '<=', (int)$filters['range_flight']);
}

// Land
if (!empty($filters['country'])) {
    $query->where('country_id', $filters['country']);
}

// Klimazone
if (!empty($filters['climate_zone'])) {
    $query->where('climate_lnam', 'LIKE', '%' . $filters['climate_zone'] . '%');
}



        // Aktivitäten Filter
        if (!empty($filters['activities'])) {
            foreach ($filters['activities'] as $activity => $value) {
                if ($value) {
                    $column = $this->getActivityColumn($activity);
                    if ($column) {
                        $query->where($column, 1);
                    }
                }
            }
        }

        // Suchbegriff Filter
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('text_short', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhereHas('country', function($q2) use ($searchTerm) {
                      $q2->where('title', 'LIKE', '%' . $searchTerm . '%');
                  });
            });
        }

        return $query;
    }

    /**
     * Get filtered locations count based on criteria
     */
    public function getFilteredCount(array $filters = []): int
    {
        $query = $this->buildSearchQuery($filters);
        return $query->count();
    }

    /**
     * Get preview results for AJAX updates
     */
    public function getPreviewResults(array $filters = [], int $limit = 5)
    {
        $query = $this->buildSearchQuery($filters);

        return $query->limit($limit)
            ->with('country')
            ->get();
    }

    /**
     * Map activity key to database column
     */
    protected function getActivityColumn(string $activity): ?string
    {
        $mapping = [
            'beach' => 'list_beach',
            'citytravel' => 'list_citytravel',
            'sports' => 'list_sports',
            'island' => 'list_island',
            'culture' => 'list_culture',
            'nature' => 'list_nature',
            'watersport' => 'list_watersport',
            'wintersport' => 'list_wintersport',
            'mountainsport' => 'list_mountainsport',
            'biking' => 'list_biking',
            'fishing' => 'list_fishing',
            'amusement_park' => 'list_amusement_park',
            'water_park' => 'list_water_park',
            'animal_park' => 'list_animal_park',
        ];

        return $mapping[$activity] ?? null;
    }

    // [Hier bleiben alle anderen bestehenden Methoden...]

    /**
     * Get all continents
     */
    public function getContinents()
    {
        return WwdeContinent::orderBy('title')->get();
    }

    /**
     * Get active countries ordered by title
     */
    public function getActiveCountries()
    {
        return WwdeCountry::active()
            ->with('continent')
            ->orderBy('title')
            ->get();
    }

    /**
     * Get unique currencies from active countries
     */
    public function getCurrencies(): array
    {
        $currencies = WwdeCountry::select([
                'currency_code',
                'currency_name',
                DB::raw('COUNT(*) as country_count')
            ])
            ->active()
            ->whereNotNull('currency_code')
            ->where('currency_code', '!=', '')
            ->groupBy('currency_code', 'currency_name')
            ->orderBy('currency_name')
            ->get()
            ->mapWithKeys(function ($country) {
                $label = $country->currency_name;
                if (!empty($country->currency_code)) {
                    $label .= " ({$country->currency_code})";
                }
                if ($country->country_count > 1) {
                    $label .= " - {$country->country_count} Länder";
                }
                return [$country->currency_code => $label];
            })
            ->toArray();

        // Füge "Beliebig" Option hinzu
        return ['' => 'Beliebig'] + $currencies;
    }

    /**
     * Get unique languages from active countries
     */
    public function getLanguages(): array
    {
        $languages = WwdeCountry::select('official_language')
            ->active()
            ->whereNotNull('official_language')
            ->where('official_language', '!=', '')
            ->groupBy('official_language')
            ->orderBy('official_language')
            ->pluck('official_language', 'official_language')
            ->toArray();

        return ['' => 'Beliebig'] + $languages;
    }

    /**
     * Get flight duration options
     */
    public function getFlightDurations(): array
    {
        // Basierend auf den Daten aus wwde_locations Tabelle
        $durations = [
            '' => 'Beliebig',
            'short' => 'Kurzstrecke (bis 4h)',
            'medium' => 'Mittelstrecke (4-8h)',
            'long' => 'Langstrecke (über 8h)',
        ];

        return $durations;
    }

    /**
     * Get climate zones from countries
     */
    public function getClimateZones(): array
    {
        $zones = [];

        // Hole alle aktiven Länder mit Klimazonen
        $countries = WwdeCountry::active()
            ->select('climatezones_lnam')
            ->whereNotNull('climatezones_lnam')
            ->where('climatezones_lnam', '!=', '')
            ->get();

        foreach ($countries as $country) {
            if ($country->climatezones_lnam) {
                // Trenne bei "und" und bereinige
                $splitZones = preg_split('/\s+und\s+/', $country->climatezones_lnam);

                foreach ($splitZones as $zone) {
                    $zone = trim($zone);
                    // Entferne "der " oder "den " Präfixe
                    $zone = preg_replace('/^(der|den)\s+/', '', $zone);

                    if (!empty($zone) && !in_array($zone, $zones)) {
                        $zones[] = $zone;
                    }
                }
            }
        }

        sort($zones);

        return ['' => 'Beliebig'] + array_combine($zones, $zones);
    }

    /**
     * Get price tendencies
     */
    public function getPriceTendencies(): array
    {
        $tendencies = WwdeCountry::active()
            ->select('price_tendency')
            ->whereNotNull('price_tendency')
            ->where('price_tendency', '!=', '')
            ->groupBy('price_tendency')
            ->orderByRaw("FIELD(price_tendency, 'Niedrig', 'Mittel', 'Hoch')")
            ->pluck('price_tendency', 'price_tendency')
            ->toArray();

        return ['' => 'Beliebig'] + $tendencies;
    }

    /**
     * Get activity options based on wwde_locations columns
     */
    public function getActivities(): array
    {
        // Basierend auf den Spalten in wwde_locations
        return [
            '' => 'Beliebig',
            'beach' => 'Strand',
            'citytravel' => 'Städtereise',
            'sports' => 'Sport',
            'island' => 'Insel',
            'culture' => 'Kultur',
            'nature' => 'Natur',
            'watersport' => 'Wassersport',
            'wintersport' => 'Wintersport',
            'mountainsport' => 'Bergsport',
            'biking' => 'Radfahren',
            'fishing' => 'Angeln',
            'amusement_park' => 'Vergnügungspark',
            'water_park' => 'Wasserpark',
            'animal_park' => 'Tierpark',
        ];
    }

    /**
     * Get months for travel time selection
     */
    public function getMonths(): array
    {
        $months = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
        }

        return ['' => 'Beliebig'] + $months;
    }

/**
 * Get filtered results with pagination
 */
public function getFilteredResults(array $filters, int $perPage = 12)
{
    try {
        return $this->buildSearchQuery($filters)
            ->orderBy(
                $filters['sort_by'] ?? 'title',
                $filters['sort_direction'] ?? 'asc'
            )
            ->paginate($perPage);

    } catch (\Exception $e) {
        Log::error('Fehler in getFilteredResults: ' . $e->getMessage());
        Log::error($e->getTraceAsString());

        return collect(); // fallback
    }
}



}
