<?php

// App/Services/DetailSearchService.php
namespace App\Services\Search;


use App\Models\WwdeLocation;
use App\Repositories\DetailSearchRepository;

class DetailSearchService
{
    protected $repository;

    public function __construct(DetailSearchRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getFilterOptions(): array
    {
        return $this->repository->getFilterOptions();
    }

    public function getResultCount(array $filters): int
    {
        $query = $this->buildQuery($filters);
        return $query->count();
    }

    public function getResults(array $filters)
    {
        $query = $this->buildQuery($filters);

        // Pagination
        return $query->paginate(20)
            ->appends($filters);
    }

    protected function buildQuery(array $filters)
    {
        $query = WwdeLocation::query()->active();

        // Kontinente
        if (!empty($filters['continents'])) {
            $continentIds = array_keys(array_filter($filters['continents']));
            $query->whereIn('continent_id', $continentIds);
        }

        // Länder
        if (!empty($filters['country'])) {
            $query->where('country_id', $filters['country']);
        }

        // Klimazone
        if (!empty($filters['climate_zone'])) {
            $query->where('climate_lnam', 'LIKE', '%' . $filters['climate_zone'] . '%');
        }

        // Monat (basierend auf best_traveltime_json)
        if (!empty($filters['month'])) {
            $query->whereJsonContains('best_traveltime_json', (int)$filters['month']);
        }

        // Aktivitäten (z.B. list_beach, list_citytravel)
        if (!empty($filters['activities'])) {
            foreach ($filters['activities'] as $activity) {
                $query->where($activity, 1);
            }
        }

        // Preisspanne
        if (!empty($filters['price_range'])) {
            $query->whereBetween('price_flight', $this->parsePriceRange($filters['price_range']));
        }

        return $query;
    }
}
