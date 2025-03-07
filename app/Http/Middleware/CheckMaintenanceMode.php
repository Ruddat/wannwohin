<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\MaintenanceService;

class CheckMaintenanceMode
{
    protected $maintenanceService;

    public function __construct(MaintenanceService $maintenanceService)
    {
        $this->maintenanceService = $maintenanceService;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($this->maintenanceService->isMaintenanceModeActive()) {
            $userIp = $request->ip();
            // Erlaube Zugriff für erlaubte IPs oder Admins (falls du eine is_admin Spalte hast)
            if ($this->maintenanceService->isIpAllowed($userIp) || ($request->user() && $request->user()->is_admin)) {
                return $next($request);
            }

            return response()->view('maintenance', [
                'message' => $this->maintenanceService->getMaintenanceMessage()
            ], 503);
        }

        return $next($request);
    }
}
