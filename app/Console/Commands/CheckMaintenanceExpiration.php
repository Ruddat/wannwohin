<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MaintenanceService;

class CheckMaintenanceExpiration extends Command
{
    protected $signature = 'maintenance:check-expiration';
    protected $description = 'Prüft, ob der Wartungsmodus abgelaufen ist, und deaktiviert ihn';

    public function handle(MaintenanceService $maintenanceService)
    {
        if ($maintenanceService->isMaintenanceModeActive()) {
            $this->info('Wartungsmodus ist noch aktiv.');
        } else {
            $this->info('Wartungsmodus ist nicht aktiv oder wurde deaktiviert.');
        }
    }
}
