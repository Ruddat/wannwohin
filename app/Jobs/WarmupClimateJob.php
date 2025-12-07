<?php

namespace App\Jobs;

use App\Models\WwdeLocation;
use Illuminate\Foundation\Queue\Queueable;

use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\LocationDetails\ClimateDataService;

class WarmupClimateJob implements ShouldQueue
{
    public function handle()
    {
WwdeLocation::chunk(200, function ($items) {
    foreach ($items as $loc) {
        app(ClimateDataService::class)->import($loc);
    }
});
    }
}
