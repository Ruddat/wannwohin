<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MaintenanceService;

class DisableMaintenanceMode extends Command
{
    protected $signature = 'maintenance:disable';
    protected $description = 'Deaktiviert den Wartungsmodus';

    public function handle(MaintenanceService $maintenanceService)
    {
        $maintenanceService->disableMaintenanceMode();
        $this->info('Wartungsmodus wurde deaktiviert.');
    }
}
