<?php

namespace App\Models;

use App\Models\ModProviders;
use Illuminate\Database\Eloquent\Model;

class ModAdvertisementBlocks extends Model
{
    protected $fillable = [
        'title',
        'content',
        'link',
        'type',
        'script',
        'position',
        'provider_id',
        'is_active',
    ];


    protected $casts = [
        'position' => 'array', // Position als Array casten
        'is_active' => 'boolean',
    ];

    
    public function provider()
    {
        return $this->belongsTo(ModProviders::class, 'provider_id');
    }
}
