<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaintenanceService
{
    private function getSetting($key, $default = null)
    {
        return \App\Models\ModSiteSettings::get($key, $default);
    }

    public function isMaintenanceModeActive()
    {
        $isActive = $this->getSetting('maintenance_mode', false);
        $startAt = $this->getSetting('maintenance_start_at');
        $endAt = $this->getSetting('maintenance_end_at');
        $now = Carbon::now();

        if ($startAt && $endAt) {
            $start = Carbon::parse($startAt);
            $end = Carbon::parse($endAt);

            // Wenn die Zeit abgelaufen ist, deaktiviere den Wartungsmodus
            if ($isActive && $now->greaterThan($end)) {
                $this->disableMaintenanceMode();
                return false;
            }

            return $isActive && $now->between($start, $end);
        }

        return $isActive;
    }

    public function getMaintenanceMessage()
    {
        return $this->getSetting('maintenance_message', 'Die Seite befindet sich im Wartungsmodus. Bitte später wiederkommen!');
    }

    public function isIpAllowed($ip)
    {
        $allowedIps = $this->getSetting('maintenance_allowed_ips', []);
        return in_array($ip, $allowedIps);
    }

    public function enableMaintenanceMode($message = null, $startAt = null, $endAt = null, $allowedIps = [])
    {
        DB::table('mod_site_settings')->updateOrInsert(
            ['key' => 'maintenance_mode'],
            ['value' => '1', 'type' => 'boolean', 'updated_at' => now()]
        );
        DB::table('mod_site_settings')->updateOrInsert(
            ['key' => 'maintenance_message'],
            ['value' => $message ?? $this->getMaintenanceMessage(), 'type' => 'string', 'updated_at' => now()]
        );
        DB::table('mod_site_settings')->updateOrInsert(
            ['key' => 'maintenance_start_at'],
            ['value' => $startAt ? Carbon::parse($startAt)->toIso8601String() : null, 'type' => 'string', 'updated_at' => now()]
        );
        DB::table('mod_site_settings')->updateOrInsert(
            ['key' => 'maintenance_end_at'],
            ['value' => $endAt ? Carbon::parse($endAt)->toIso8601String() : null, 'type' => 'string', 'updated_at' => now()]
        );
        DB::table('mod_site_settings')->updateOrInsert(
            ['key' => 'maintenance_allowed_ips'],
            ['value' => json_encode($allowedIps), 'type' => 'json', 'updated_at' => now()]
        );

        Cache::forget('site_setting_maintenance_mode');
        Cache::forget('site_setting_maintenance_message');
        Cache::forget('site_setting_maintenance_start_at');
        Cache::forget('site_setting_maintenance_end_at');
        Cache::forget('site_setting_maintenance_allowed_ips');
    }

    public function disableMaintenanceMode()
    {
        DB::table('mod_site_settings')->updateOrInsert(
            ['key' => 'maintenance_mode'],
            ['value' => '0', 'type' => 'boolean', 'updated_at' => now()]
        );
        DB::table('mod_site_settings')->whereIn('key', [
            'maintenance_start_at',
            'maintenance_end_at'
        ])->update(['value' => null, 'updated_at' => now()]);

        Cache::forget('site_setting_maintenance_mode');
        Cache::forget('site_setting_maintenance_start_at');
        Cache::forget('site_setting_maintenance_end_at');
    }
}
