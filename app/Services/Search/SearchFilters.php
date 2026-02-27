<?php

namespace App\Services\Search;

use Illuminate\Http\Request;

class SearchFilters
{
    public ?int $continent = null;
    public ?int $country = null;
    public ?int $month = null;
    public ?int $priceRange = null;

    public ?int $sunshineMin = null;
    public ?int $waterTempMin = null;
    public ?int $dailyTempMin = null;
    public ?int $dailyTempMax = null;

    public array $activities = [];

    public ?string $language = null;
    public ?string $currency = null;
    public ?int $visum = null;
    public ?string $priceTendency = null;

    public ?int $flightDuration = null;
    public ?int $distance = null;

    // 🔥 TAG SYSTEM
    public array $tags = [];

    // 🔥 BESTE REISEZEIT
    public bool $bestTimeOnly = false;

    // 🔥 TAG LOGIC: 'and' | 'or'
    public string $tagMode = 'and';


    public static function fromRequest(Request $request): self
    {
        $filters = new self();

        $filters->continent      = $request->integer('continent');
        $filters->country        = $request->integer('country');
        $filters->month          = $request->integer('month');
        $filters->priceRange     = $request->integer('price');

        $filters->sunshineMin    = $request->integer('sunshine_min');
        $filters->waterTempMin   = $request->integer('water_temp_min');
        $filters->dailyTempMin   = $request->integer('daily_temp_min');
        $filters->dailyTempMax   = $request->integer('daily_temp_max');

        $filters->activities     = $request->input('activities', []);

        $filters->language       = $request->input('language');
        $filters->currency       = $request->input('currency');
        $filters->visum          = $request->has('visum')
            ? ($request->input('visum') === 'yes' ? 1 : 0)
            : null;

        $filters->priceTendency  = $request->input('price_tendency');
        $filters->flightDuration = $request->integer('flight_duration');
        $filters->distance       = $request->integer('distance');

        return $filters;
    }
}
