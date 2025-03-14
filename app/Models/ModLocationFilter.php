<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModLocationFilter extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'text_type',
        'uschrift',
        'text',
        'category',
        'addinfo',
        'is_active',
        
    ];

    public function location()
    {
        return $this->belongsTo(WwdeLocation::class, 'location_id');
    }
}
