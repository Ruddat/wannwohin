<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModVisitorSession extends Model
{
    protected $fillable = [
        'session_id',
        'ip_address',
        'page_url',
        'dwell_time',
        'last_activity_at',
        'started_at',
    ];

    public $timestamps = false; // Keine Standard-Timestamps, da wir last_activity_at und started_at nutzen

    public function getDwellTimeFormattedAttribute()
    {
        $seconds = $this->dwell_time;
        return sprintf('%02d:%02d:%02d', ($seconds / 3600), ($seconds / 60 % 60), $seconds % 60);
    }

    public function isOnline()
    {
        return now()->diffInMinutes($this->last_activity_at) < 5; // Online, wenn letzte Aktivität < 5 Minuten
    }
}
