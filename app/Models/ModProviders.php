<?php

namespace App\Models;

use App\Models\ModAdvertisementBlocks;
use Illuminate\Database\Eloquent\Model;

class ModProviders extends Model
{
    protected $guarded = []; // Alles ausfüllbar außer guarded Felder (z. B. id)

    // Beziehung zu Werbeblöcken
    public function advertisements()
    {
        return $this->hasMany(ModAdvertisementBlocks::class, 'provider_id');
    }

    // Optional: Ein Accessor für einen vollständigen Anbieter-String
    public function getFullInfoAttribute()
    {
        return "{$this->name} (" . ($this->email ? $this->email : 'Kein E-Mail') . ")";
    }
}
