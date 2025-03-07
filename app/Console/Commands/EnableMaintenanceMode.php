<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MaintenanceService;

class EnableMaintenanceMode extends Command
{
    protected $signature = 'maintenance:enable {--message=} {--start=} {--end=} {--ips=}';
    protected $description = 'Aktiviert den Wartungsmodus';

    public function handle(MaintenanceService $maintenanceService)
    {
        $message = $this->option('message');
        $start = $this->option('start');
        $end = $this->option('end');
        $ips = $this->option('ips') ? explode(',', $this->option('ips')) : [];

        $maintenanceService->enableMaintenanceMode($message, $start, $end, $ips);
        $this->info('Wartungsmodus wurde aktiviert.');
    }
}
